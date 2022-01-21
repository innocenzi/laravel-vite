import type { SSROptions } from 'vite'

export interface PhpConfiguration {
	ssr_entrypoint?: string
	entrypoints: string | string[]
	build_path: string
	dev_url: string
	aliases?: Record<string, string>
	public_directory?: string
	commands?: string[]
}

export interface Options {
	/**
	 * Path to PHP executable.
	 */
	phpExecutable?: string

	/**
	 * A configuration object or a path to a configuration file.
	 * Setting to false disables reading the configuration file path from the `CONFIG_PATH_VITE` environment variable.
	 */
	config?: PhpConfiguration | string | false

	/**
	 * Post CSS plugins.
	 */
	postcss?: any[]

	/**
	 * Prefixes to use to expose environment variables to the scripts.
	 */
	envPrefix?: string | string[]

	/**
	 * SSR-specific options.
	 */
	ssr?: SSROptions
}
