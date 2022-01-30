import type { SSROptions } from 'vite'

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
	public_directory: string
	dev_server: {
		url: string
		cert?: string
		key?: string
	}
	commands?: CommandsConfiguration
	env_prefixes?: string[]
}

export type ResolvedConfiguration = ViteConfiguration & {
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
	 */
	updateTsConfig?: boolean
}

export interface Certificates {
	key: string
	cert: string
}
