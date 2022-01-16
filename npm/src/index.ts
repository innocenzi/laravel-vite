import path from 'path'
import { homedir } from 'os'
import { Plugin as PostCSSPlugin } from 'postcss'
import { Plugin, UserConfig } from 'vite'
import deepmerge from 'deepmerge'
import execa from 'execa'
import chalk from 'chalk'
import dotenv from 'dotenv'
import dotenvExpand from 'dotenv-expand'
import makeDebugger from 'debug'
import { manifestPlugin } from './manifest-plugin'

type VitePlugin = Plugin | ((...params: any[]) => Plugin)
interface PhpConfiguration {
	build_path?: string
	dev_url?: string
	entrypoints?: false | string | string[]
	aliases?: Record<string, string>
	public_directory?: string
	asset_plugin?: {
		find_regex?: string
		replace_with?: string
	}
	commands?: string[]
}

const debug = makeDebugger('vite:laravel')

/**
 * A plugin enabling HMR for Blade files.
 */
export const bladeReload = (): Plugin => ({
	name: 'vite:laravel:blade',
	handleHotUpdate({ file, server }) {
		// This might need more granular control. Maybe a configuration
		// option. Feel free to open an issue or a PR.
		if (file.endsWith('.blade.php') || file.endsWith('vite.php')) {
			server.ws.send({
				type: 'full-reload',
				path: '*',
			})
		}
	},
})

/**
 * A plugin fixing Vite-related asset issues.
 * @see https://github.com/innocenzi/laravel-vite/issues/31
 */
export const staticAssetFixer = (regex: RegExp, replaceWith: string): Plugin => ({
	name: 'static-asset-fixer',
	enforce: 'post',
	apply: 'serve',
	transform: (code) => ({
		code: code.replace(regex, replaceWith),
		map: null,
	}),
})

export class ViteConfiguration {
	public publicDir: string
	public build: UserConfig['build']
	public server: UserConfig['server']
	public plugins: UserConfig['plugins']
	public base: UserConfig['base']
	public resolve: UserConfig['resolve']
	public envPrefix: UserConfig['envPrefix']

	constructor(
		config: (UserConfig | ((env: typeof process.env) => UserConfig)) = {},
		artisan: PhpConfiguration = {},
	) {
		dotenvExpand(dotenv.config())
		debug('Loaded configuration with dotenv')

		// Sets the env prefixes
		this.envPrefix = ['VITE_', 'MIX_']

		// Sets the base directory.
		this.base = process.env.ASSET_URL ?? ''

		// Makes sure the base ends with a slash.
		if (!this.base.endsWith('/')) {
			this.base += '/'
		}

		// In production, we want to append the build_path. It is not needed in development,
		// since assets are served from the development server's root, but we're writing
		// generated assets in public/build_path, so build_path needs to be referenced.
		if (process.env.NODE_ENV?.startsWith('prod') || process.env.APP_ENV !== 'local') {
			debug('Running in production, adding build path to base')
			this.base += artisan.build_path ?? ''

			if (!this.base.endsWith('/')) {
				this.base += '/'
			}
		}

		debug('Set base URL:', this.base)

		this.plugins = []
		this.publicDir = artisan.public_directory ?? 'resources/static'
		this.build = {
			manifest: true,
			outDir: artisan?.build_path
				? `public/${artisan.build_path}`
				: 'public/build',
			rollupOptions: {
				input: [],
			},
		}

		debug('Set build configuration:', this.build)

		// Adds the blade reload plugin.
		this.plugins.push(bladeReload())

		// Adds the manifest patch plugin.
		this.plugins.push(manifestPlugin())

		// Registers aliases.
		if (artisan?.aliases) {
			this.resolve = {
				alias: Object.fromEntries(
					Object.entries(artisan.aliases).map(([alias, directory]) => {
						return [alias, path.join(process.cwd(), directory)]
					}),
				),
			}

			debug('Registered aliases:', this.resolve.alias)
		}

		if (artisan?.dev_url) {
			const [protocol, host, port] = artisan.dev_url.split(':')

			// Configures the development server and HMR
			this.server = {
				host: host.substr(2),
				https: protocol === 'https',
				port: port ? Number(port) : 3000,
				origin: `${protocol}:${host}:${port}`,
				hmr: {
					host: host.substr(2),
					port: Number(port) || 3000,
				},
			}

			debug('Configured server:', this.server)

			// Pushes entrypoints as build inputs
			if (artisan?.entrypoints) {
				(this.build.rollupOptions!.input! as string[]).push(...artisan.entrypoints)
			}

			debug('Configured entrypoints:', this.build.rollupOptions!.input!)

			// Fixes the asset loading in development
			if (artisan?.asset_plugin?.find_regex && artisan?.asset_plugin?.replace_with) {
				const regex = new RegExp(artisan?.asset_plugin?.find_regex, 'g')
				const replace = artisan.dev_url + artisan?.asset_plugin?.replace_with

				this.plugins.push(staticAssetFixer(regex, replace))
				debug('Registered asset-fixing plugin:', { regex, replace })
			}

			this.merge(config)
		}

		// Run commands
		if (artisan?.commands) {
			for (const command of artisan.commands) {
				const parts = Array.isArray(command) ? command : [command]

				debug('Running:', parts.join(' '))
				debug(callArtisan(...parts))
			}
		}
	}

	/**
	 * Configures the development server to use Valet's SSL certificates.
	 */
	public withValetCertificates(options?: { domain?: string; path?: string }): this {
		const home = homedir()
		const domain = options?.domain ?? process.env.APP_URL?.replace(/^https?:\/\//, '')
		let path = options?.path ?? '/.config/valet/Certificates/'

		if (!domain) {
			console.warn('No domain specified. Certificates will not be applied.')

			return this
		}

		if (!path.endsWith('/')) {
			path = `${path}/`
		}

		return this.withCertificates(`${home}${path}${domain}.key`, `${home}${path}${domain}.crt`)
	}

	/**
	 * Configures the development server to use Laragon's SSL certificates.
	 */
	public withLaragonCertificates(path?: string): this {
		path ??= 'C:\\laragon'

		if (path.endsWith('\\')) {
			path = path.slice(0, -1)
		}

		return this.withCertificates(`${path}\\etc\\ssl\\laragon.key`, `${path}\\etc\\ssl\\laragon.crt`)
	}

	/**
	 * Configures the development server to use the certificates at the given paths.
	 */
	public withCertificates(): this
	public withCertificates(callback: (env: typeof process.env) => [string, string]): this
	public withCertificates(key: string, cert: string): this
	public withCertificates(callbackOrKey?: string | ((env: typeof process.env) => [string, string]), cert?: string): this {
		if (!callbackOrKey && !cert) {
			callbackOrKey = process.env.DEV_SERVER_KEY
			cert = process.env.DEV_SERVER_CERT
		}

		if (typeof callbackOrKey === 'function') {
			[callbackOrKey, cert] = callbackOrKey(process.env)
		}

		if (!callbackOrKey && !cert) {
			console.warn(chalk.yellow.bold('(!) Certificates are not configured properly. Ensure your environment file is has "DEV_SERVER_KEY" and "DEV_SERVER_CERT".'))

			return this
		}

		return this.merge({
			server: {
				https: {
					maxVersion: 'TLSv1.2',
					key: callbackOrKey as string,
					cert,
				},
			},
		})
	}

	/**
	 * Configures PostCSS with the given plugins.
	 */
	public withPostCSS(plugins: PostCSSPlugin[] = []): this {
		return this.merge({
			css: {
				postcss: {
					plugins,
				},
			},
		})
	}

	/**
	 * Defines the directory which contains static assets.
	 * Defaults to resources/static.
	 */
	public withStaticAssets(publicDir: string): this {
		this.publicDir = publicDir

		return this
	}

	/**
	 * Defines the directory in which the assets will be generated.
	 * Defaults to public/build.
	 */
	public withOutput(outDir: string): this {
		this.build!.outDir = outDir

		return this
	}

	/**
	 * @deprecated
	 */
	public withEntry(...entries: string[]): this {
		throw new Error('Adding entrypoints via vite.config.ts is no longer supported, use config/vite.php instead.')

		return this
	}

	/**
	 * @deprecated
	 */
	public withEntries(...entries: string[]): this {
		return this.withEntry(...entries)
	}

	/**
	 * Adds the given Vite plugin.
	 *
	 * @example
	 * import vue from "@vitejs/plugin-vue"
	 *
	 * export default defineConfig()
	 *	.withPlugin(vue)
	 */
	public withPlugin(plugin: VitePlugin): this {
		if (typeof plugin === 'function') {
			plugin = plugin()
		}

		this.plugins!.push(plugin)

		return this
	}

	/**
	 * Adds the given Vite plugins.
	 *
	 * @example
	 * import vue from "@vitejs/plugin-vue"
	 * import components from "vite-plugin-components"
	 *
	 * export default defineConfig()
	 *	.withPlugins(vue, components)
	 */
	public withPlugins(...plugins: VitePlugin[]): this {
		plugins.forEach((plugin) => this.withPlugin(plugin))

		return this
	}

	/**
	 * Merges in the given Vite configuration.
	 */
	public merge(config: UserConfig | ((env: typeof process.env) => UserConfig)): this {
		if (typeof config === 'function') {
			config = config(process.env)
		}

		const result: UserConfig = deepmerge(this, config)

		if (Reflect.has(config, 'base')) {
			console.warn(chalk.yellow.bold('(!) "base" option should not be used with Laravel Vite. Use the "ASSET_URL" environment variable instead.'))
		}

		for (const [key, value] of Object.entries(result)) {
			// @ts-expect-error
			this[key] = value
		}

		debug('Merged configuration', config)

		return this
	}
}

/**
 * Gets the configuration from this package's artisan command.
 */
function getConfigurationFromArtisan(): PhpConfiguration | undefined {
	try {
		return JSON.parse(callArtisan('vite:config')) as PhpConfiguration
	} catch (error) {
		console.warn('Could not read configuration from PHP.')
		console.error(error)
	}
}

/**
 * Calls an artisan command.
 */
export function callArtisan(...params: string[]): string {
	return execa.sync('php', ['artisan', ...params])?.stdout
}

/**
 * Creates a Vite configuration object, simplified for use with
 * Laravel.
 *
 * @deprecated Use `defineConfig` instead
 */
export function createViteConfiguration() {
	return defineConfig()
}

/**
 * Creates a Vite configuration object, simplified for use with
 * Laravel.
 *
 * @see https://github.com/innocenzi/laravel-vite
 */
export function defineConfig(config: UserConfig = {}, artisan?: PhpConfiguration) {
	return new ViteConfiguration(config, artisan ?? getConfigurationFromArtisan())
}

export default defineConfig
