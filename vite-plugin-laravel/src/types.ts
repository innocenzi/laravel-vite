import type { SSROptions } from 'vite'

export interface PhpConfiguration {
	ssr_entrypoint?: string
	entrypoints?: string | string[]
	build_path?: string
	dev_url?: string
	aliases?: Record<string, string>
	public_directory?: string
	commands?: string[]
	ssr?: {
		entrypoint: string
	}
}

export interface Options {
	/**
	 * Path to PHP executable.
	 */
	phpExecutable?: string

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
