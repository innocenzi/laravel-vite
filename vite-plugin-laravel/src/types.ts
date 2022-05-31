import type { SSROptions, ViteDevServer } from 'vite'

export interface ServerConfiguration {
	default: keyof ServerConfiguration['configs']
	aliases: Record<string, string>
	configs: Record<string, ViteConfiguration>
	commands: CommandsConfiguration
}

export interface CommandsConfiguration {
	artisan: Record<string, string[]> | string[]
	shell: Record<string, string[]> | string[]
}

export interface ViteConfiguration {
	entrypoints: {
		paths: string | string[]
		ssr?: string
		ignore?: string | string[]
	}
	build_path: string
	dev_server: {
		enabled?: boolean
		url: string
		cert?: string
		key?: string
	}
	commands?: CommandsConfiguration
	env_prefixes?: string[]
}

export type ResolvedConfiguration = ViteConfiguration & {
	configName?: string
	aliases: Record<string, string>
}

export interface Options {
	/**
	 * Path to PHP executable.
	 */
	php?: string

	/**
	 * A configuration object or a path to a configuration file.
	 * Setting to false disables reading the configuration file path from the `CONFIG_PATH_VITE` environment variable.
	 */
	config?: ResolvedConfiguration | string | false

	/**
	 * Post CSS plugins.
	 */
	postcss?: any[]

	/**
	 * SSR-specific options.
	 */
	ssr?: SSROptions

	/**
	 * Whether to automatically update the tsconfig.json file with aliases.
	 *
	 * @deprecated Use `vite.commands.artisan` => `vite:tsconfig` instead.
	 */
	updateTsConfig?: boolean

	/**
	 * Whether to allow overrides from the base configuration. If false, base
	 * options will be ignored, so stuff like `--host 0.0.0.0` won't work.
	 *
	 * @default true
	 */
	allowOverrides?: boolean

	/**
	 * List of file changes to listen to.
	 */
	watch?: WatchInput[] | WatchOptions
}

export interface WatchOptions {
	reloadOnBladeUpdates?: boolean
	reloadOnConfigUpdates?: boolean
	input?: WatchInput[]
}

export interface WatchInputHandlerParameters {
	file: string
	server: ViteDevServer
}

export interface WatchInput {
	condition: (file: string) => boolean
	handle: (parameters: WatchInputHandlerParameters) => void
}

export interface Certificates {
	key: string
	cert: string
}

export interface PhpFinderOptions {
	/**
	 * Actual path to PHP. This will be used instead of
	 */
	path?: string

	/**
	 * Custom environment variables.
	 */
	env?: any

	/**
	 * Either `production` or `development`. Used for loading the environment.
	 */
	mode?: string
}
