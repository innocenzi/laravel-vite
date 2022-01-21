import path from 'node:path'
import makeDebugger from 'debug'
import { execaSync } from 'execa'
import { Plugin, UserConfig, loadEnv } from 'vite'
import { finish, wrap } from './utils'
import type { Options, PhpConfiguration } from './types'

const PREFIX = 'Laravel Vite'
const CONFIG_ARTISAN_COMMAND = 'vite:config'
const debug = makeDebugger('laravel:config')

/**
 * Calls an artisan command.
 */
export function callArtisan(executable: string, ...params: string[]): string {
	return execaSync(executable, ['artisan', ...params])?.stdout
}

/**
 * Reads the configuration from the `php artisan vite:config` command.
 * @param executable
 * @returns
 */
export function readConfig(executable: string): PhpConfiguration {
	try {
		return JSON.parse(callArtisan(executable, CONFIG_ARTISAN_COMMAND)) as PhpConfiguration
	} catch (error: any) {
		throw new Error(`[${PREFIX}] Could not read configuration from PHP: ${error.message}`)
	}
}

/**
 * Loads the Laravel Vite configuration.
 */
export const config = (options: Options = {}): Plugin => ({
	name: 'laravel:config',
	enforce: 'post',
	config: (baseConfig, { command, mode }) => {
		// Loads .env
		const env = loadEnv(mode, process.cwd(), '')

		// Loads artisan
		const artisan = readConfig(env.PHP_EXECUTABLE || options?.phpExecutable || 'php')
		debug('Configuration from PHP:', artisan)

		// Sets base
		const base = finish(`${finish(env.ASSET_URL, '/', '')}${command === 'build' ? `${artisan.build_path}/` : ''}`, '/')
		debug('Base URL:', base)

		// Parses dev url
		const { protocol, hostname, port } = new URL(artisan.dev_url || 'http://localhost:3000')
		const key = env.DEV_SERVER_KEY
		const cert = env.DEV_SERVER_CERT
		const usesHttps = key && cert && protocol === 'https:'
		debug('Uses HTTPS:', usesHttps, { key, cert, protocol, hostname, port })

		// Entrypoints
		const ssr = process.argv.includes('--ssr')
		const entrypoints = ssr ? artisan.ssr_entrypoint : artisan.entrypoints

		// Returns config
		const config: UserConfig = {
			envPrefix: wrap(options.envPrefix, ['VITE_', 'MIX_']),
			base,
			publicDir: artisan.public_directory ?? 'resources/static',
			server: {
				host: hostname,
				https: usesHttps
					? { maxVersion: 'TLSv1.2', key, cert }
					: protocol === 'https:',
				port: port ? Number(port) : 3000,
				origin: `${protocol}:${hostname}:${port}`,
				hmr: {
					host: hostname,
					port: Number(port) || 3000,
				},
			},
			build: {
				ssrManifest: ssr,
				manifest: !ssr,
				ssr,
				outDir: `public/${artisan.build_path ?? 'build'}`,
				rollupOptions: {
					input: entrypoints,
				},
			},
			resolve: {
				alias: Object.fromEntries(Object.entries(artisan.aliases || {}).map(([alias, directory]) => {
					return [alias, path.join(process.cwd(), directory)]
				})),
			},
			css: { postcss: options.postcss ? { plugins: options.postcss } : baseConfig.css?.postcss },
		}

		return config
	},
})

// const laravelVite = (): Plugin[] => [
// 	blade(),
// 	inertiaLayout(),
// 	laravel(),
// ]

// export default defineConfig({
// 	css: {
// 		postcss: {
// 			plugins: [tailwindcss(), autoprefixer()],
// 		},
// 	},
// 	plugins: [
// 		laravelVite(),
// 		vue(),
// 	],
// })
