import path from 'path'
import { homedir } from 'os'
import { Plugin as PostCSSPlugin } from 'postcss'
import { Plugin, UserConfig } from 'vite'
import deepmerge from 'deepmerge'
import execa from 'execa'
import chalk from 'chalk'
import dotenv from 'dotenv'

type VitePlugin = Plugin | ((...params: any[]) => Plugin)

interface PhpConfiguration {
	build_path?: string
	dev_url?: string
	entrypoints?: false | string | string[]
	aliases?: Record<string, string>
	public_directory?: string
}

/**
 * A plugin enabling HMR for Blade files.
 */
export const laravel = (): Plugin => ({
	name: 'vite:laravel',
	handleHotUpdate({ file, server }) {
		// This might need more granular control. Maybe a configuration
		// option. Feel free to open an issue or a PR.
		if (file.endsWith('.blade.php')) {
			server.ws.send({
				type: 'full-reload',
				path: '*',
			})
		}
	},
})

export class ViteConfiguration {
	public publicDir: string
	public build: UserConfig['build']
	public server: UserConfig['server']
	public plugins: UserConfig['plugins']
	public base: UserConfig['base']
	public resolve: UserConfig['resolve']

	constructor(config: UserConfig = {}, artisan: PhpConfiguration = {}) {
		dotenv.config()

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
			this.base += artisan.build_path ?? ''

			if (!this.base.endsWith('/')) {
				this.base += '/'
			}
		}

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

		this.plugins = [
			laravel(),
		]

		if (artisan?.aliases) {
			this.resolve = {
				alias: Object.fromEntries(
					Object.entries(artisan.aliases).map(([alias, directory]) => {
						return [alias, path.join(process.cwd(), directory)]
					}),
				),
			}

			generateAliases()
		}

		if (artisan?.dev_url) {
			const [protocol, host, port] = artisan.dev_url.split(':')
			this.server = {
				host: host.substr(2),
				https: protocol === 'https',
				port: port ? Number(port) : 3000,
				hmr: {
					host: host.substr(2),
					port: Number(port) || 3000,
				},
			}

			if (artisan?.entrypoints) {
				(this.build.rollupOptions!.input! as string[]).push(...artisan.entrypoints)
			}

			this.merge(config)
		}
	}

	/**
	 * Configures the development server to use Valet's SSL certificates.
	 */
	public withValetCertificates({ domain, path }: { domain?: string; path?: string }): this {
		const home = homedir()
		path ??= '/.config/valet/Certificates/'
		domain ??= process.env.APP_URL?.replace(/^https?:\/\//, '')

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
	public withCertificates(key: string, cert: string): this {
		return this.merge({
			server: {
				https: {
					maxVersion: 'TLSv1.2',
					key,
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
	 * Adds an entry point.
	 *
	 * @example
	 * export default defineConfig()
	 *	.withEntry("resources/js/app.js")
	 */
	public withEntry(...entries: string[]): this {
		this.build!.rollupOptions!.input = [
			...(this.build!.rollupOptions!.input as string[]),
			...entries,
		]

		return this
	}

	/**
	 * Adds entry points.
	 *
	 * @example
	 * export default defineConfig()
	 *	.withEntries("resources/js/app.js", "resources/js/admin.js")
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
	public merge(config: UserConfig): this {
		const result: UserConfig = deepmerge(this, config)

		if (Reflect.has(config, 'base')) {
			console.warn(chalk.yellow.bold('(!) "base" option should not be used with Laravel Vite. Use the "ASSET_URL" environment variable instead.'))
		}

		for (const [key, value] of Object.entries(result)) {
			// @ts-expect-error
			this[key] = value
		}

		return this
	}
}

/**
 * Calls an artisan command.
 */
function callArtisan(...params: string[]): string {
	return execa.sync('php', ['artisan', ...params])?.stdout
}

/**
 * Regenerates the tsconfig.json file with aliases.
 */
function generateAliases() {
	try {
		callArtisan('vite:aliases')
	} catch (error) {
		console.warn('Could not regenerate tsconfig.json.')
		console.error(error)
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

export default defineConfig()
