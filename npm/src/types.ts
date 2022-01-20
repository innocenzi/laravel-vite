
export interface PhpConfiguration {
	entrypoints?: false | string | string[]
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
}
