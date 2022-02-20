import os from 'node:os'
import fs from 'node:fs'
import path from 'node:path'
import defu from 'defu'
import c from 'chalk'
import makeDebugger from 'debug'
import { Plugin, UserConfig, loadEnv } from 'vite'
import { version } from '../package.json'
import { callArtisan, callShell, findPhpPath, finish, wrap } from './utils'
import type { Certificates, Options, ResolvedConfiguration, ServerConfiguration } from './types'

const PREFIX = 'vite:laravel:config'
const CONFIG_ARTISAN_COMMAND = 'vite:config'
const debug = makeDebugger(PREFIX)

/**
 * Loads the Laravel Vite configuration.
 */
export const config = (options: Options = {}): Plugin => {
	let serverConfig: ResolvedConfiguration
	let env: Record<string, string>

	return {
		name: 'laravel:config',
		enforce: 'post',
		config: (baseConfig, { command, mode }) => {
			// Loads .env
			env = loadEnv(mode, process.cwd(), '')

			// Infer config name
			const configName = findConfigName()
			debug('Config name:', configName ?? 'not specified')

			// Loads config
			serverConfig = readConfig(options, env, configName)
			debug('Configuration from PHP:', serverConfig)

			// Sets base
			const base = finish(`${finish(env.ASSET_URL, '/', '/')}${command === 'build' ? `${serverConfig.build_path}/` : ''}`, '/')
			debug('Base URL:', base || '<empty>')

			// Parses dev url
			const { protocol, hostname, port } = new URL(serverConfig.dev_server.url || 'http://localhost:3000')
			const { key, cert } = findCertificates(serverConfig, env, hostname)
			const usesHttps = key && cert && protocol === 'https:'
			debug('Uses HTTPS:', usesHttps, { key, cert, protocol, hostname, port })

			// Entrypoints
			const ssr = process.argv.includes('--ssr')
			const entrypoints = ssr ? serverConfig.entrypoints.ssr : serverConfig.entrypoints.paths

			// Runs commands
			const executable = findPhpPath({ env, path: options.php, mode })
			Object.entries(serverConfig.commands?.artisan ?? {}).forEach(([command, args]) => {
				if (!isNaN(+command)) {
					debug('Running artisan command without arguments:', executable, 'artisan', args)
					debug(callArtisan(executable, args))

					return
				}

				debug('Running artisan command:', executable, 'artisan', command, ...args)
				debug(callArtisan(executable, command, ...args))
			})

			Object.entries(serverConfig.commands?.shell ?? {}).forEach(([command, args]) => {
				if (!isNaN(+command)) {
					debug('Running shell command without arguments:', args)
					debug(callShell(args))

					return
				}

				debug('Running shell command:', command, ...args)
				debug(callShell(command, ...args))
			})

			// Updates aliases
			if (command !== 'build' && options.updateTsConfig) {
				// eslint-disable-next-line no-console
				console.warn(c.yellow.bold(`(!) ${c.cyan(PREFIX)} To update the tsconfig.json file, use php artisan vite:tsconfig instead. You can add it in your vite.php artisan commands.`))
			}

			// Returns config
			const resolvedConfig: UserConfig = {
				envPrefix: wrap(serverConfig.env_prefixes, ['MIX_', 'VITE_', 'SCRIPT_']),
				base,
				publicDir: false,
				server: {
					host: hostname,
					https: usesHttps
						? { maxVersion: 'TLSv1.2' as const, key, cert }
						: protocol === 'https:',
					port: port ? Number(port) : 3000,
					strictPort: !process.argv.includes('--no-strict-port'),
					origin: `${protocol}//${hostname}:${port}`,
					hmr: {
						host: hostname,
						port: Number(port) || 3000,
					},
				},
				build: {
					assetsDir: 'assets',
					ssrManifest: ssr,
					manifest: !ssr,
					ssr,
					outDir: `public/${serverConfig.build_path || 'build'}`,
					rollupOptions: {
						input: entrypoints,
					},
				},
				resolve: {
					alias: Object.fromEntries(Object.entries(serverConfig.aliases || {}).map(([alias, directory]) => {
						return [alias, path.join(process.cwd(), directory)]
					})),
				},
				css: { postcss: options.postcss ? { plugins: options.postcss } : baseConfig.css?.postcss },
			}

			// If overrides are explicitely disabled, we don't merge the configuration back
			// from the base config.
			const finalConfig = options.allowOverrides === false
				? resolvedConfig
				: defu(baseConfig, resolvedConfig)

			debug('Initial config:', baseConfig)
			debug('Resolved config:', resolvedConfig)
			debug('Final config:', finalConfig)

			return finalConfig
		},
		configureServer: (server) => {
			server.httpServer?.once('listening', () => {
				setTimeout(() => {
					server.config.logger.info(`\n  ${c.magenta(`${c.bold('LARAVEL')} v${version}`)}  ${c.dim(`(using ${c.white.bold(serverConfig.configName)} config)`)}\n`)
					server.config.logger.info(`    ${c.magenta('➜')}  ${c.bold('Application')}: ${c.cyan(env.APP_URL)}`)
					server.config.logger.info(`    ${c.magenta('➜')}  ${c.bold('Environment')}: ${c.dim(env.APP_ENV)}`)
				}, 10)
			})
		},
	}
}


/**
 * Reads the configuration from the `php artisan vite:config` command.
 */
export function readConfig(options: Options, env: NodeJS.ProcessEnv, name?: string): ResolvedConfiguration {
	const executable = findPhpPath({ env, path: options.php })
	const configFromJson = (json: any, name?: string) => {
		if (!json) {
			throw new Error('The configuration object is empty')
		}

		if (!json.configs) {
			throw new Error('The configuration object do not contain a "configs" property. Is innocenzi/laravel-vite up-to-date?')
		}

		if (name && !(name in json.configs)) {
			throw new Error(`"${name}" is not defined in "config/vite.php"`)
		}

		return <ResolvedConfiguration>{
			configName: name ?? json.default,
			commands: json.commands,
			aliases: json.aliases,
			...json.configs[name ?? json.default],
		}
	}

	try {
		// Sets path from environment variable
		if (!options.config && options.config !== false && env.CONFIG_PATH_VITE) {
			debug('Setting configuration file path to CONFIG_PATH_VITE.')
			options.config = env.CONFIG_PATH_VITE
		}

		if (typeof options.config === 'string') {
			// Reads the config from the disk
			if (fs.existsSync(options.config)) {
				debug(`Reading configuration from ${options.config}`)
				const json = JSON.parse(fs.readFileSync(options.config, { encoding: 'utf-8' })) as ServerConfiguration

				return configFromJson(json, name)
			}

			// Use the specified config name
			const json = JSON.parse(callArtisan(executable, CONFIG_ARTISAN_COMMAND)) as ServerConfiguration
			debug('Using specified configuration name:', options.config)

			return configFromJson(json, options.config)
		}

		// Returns the given config
		if (typeof options.config === 'object') {
			debug('Reading configuration from the given object.')

			return options.config
		}

		// Asks PHP for the configuration
		debug('Reading configuration from PHP.')
		const json = JSON.parse(callArtisan(executable, CONFIG_ARTISAN_COMMAND)) as ServerConfiguration

		return configFromJson(json, name)
	} catch (error: any) {
		throw new Error(`[${PREFIX}] Could not read configuration: ${error.message}`)
	}
}

/**
 * Finds the current configuration name.
 */
function findConfigName(): string | undefined {
	const configIndex = process.argv.findIndex((arg) => ['-c', '--config'].includes(arg))

	if (!configIndex) {
		return
	}

	const fileNameRegex = /vite\.([\w-]+)\.config\.ts/
	const configFile = process.argv.at(configIndex + 1)

	return fileNameRegex.exec(configFile || '')?.at(1)?.trim()
}
/**
 * Tries to find certificates from the environment.
 */
export function findCertificates(cfg: ResolvedConfiguration, env: Record<string, string>, hostname?: string): Certificates {
	let key = cfg.dev_server.key || env.DEV_SERVER_KEY || ''
	let cert = cfg.dev_server.cert || env.DEV_SERVER_CERT || ''

	if (!key || !cert) {
		switch (os.platform()) {
			case 'darwin': {
				const home = os.homedir()
				const domain = hostname
				const valetPath = '/.config/valet/Certificates/'

				key ||= `${home}${valetPath}${domain}.key`
				cert ||= `${home}${valetPath}${domain}.crt`

				debug('Automatically set certificates for Valet:', {
					home,
					domain,
					valetPath,
					key,
					cert,
				})

				break
			}

			case 'win32': {
				// Detect Laragon in PATH
				let laragonDirectory = process.env.PATH?.split(';').find((l) => l.toLowerCase().includes('laragon'))

				if (!laragonDirectory) {
					break
				}

				laragonDirectory = laragonDirectory.split('\\bin')[0]

				if (laragonDirectory.endsWith('\\')) {
					laragonDirectory = laragonDirectory.slice(0, -1)
				}

				key ||= `${laragonDirectory}\\etc\\ssl\\laragon.key`
				cert ||= `${laragonDirectory}\\etc\\ssl\\laragon.crt`

				debug('Automatically set certificates for Laragon:', {
					laragonDirectory,
					key,
					cert,
				})

				break
			}
		}
	}

	return {
		key,
		cert,
	}
}
