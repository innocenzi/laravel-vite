import { UserConfig } from "vite";
import deepmerge from "deepmerge";
import execa from "execa";

export class ViteConfiguration {
	public publicDir: string;
	public build: UserConfig["build"];
	public server: UserConfig["server"];
	public plugins: UserConfig["plugins"];

	constructor(config?: PhpConfiguration) {
		this.publicDir = "resources/static";
		this.build = {
			manifest: true,
			outDir: config?.build_path
				? `public/${config.build_path}`
				: "public/build",
			rollupOptions: {
				input: [],
			},
		};

		if (config?.dev_url) {
			const [protocol, host, port] = config.dev_url.split(":");
			this.server = {
				host: host.substr(2),
				https: protocol === "https",
				port: port ? Number(port) : 3000,
			};
		}
	}

	/**
	 * Defines the directory which contains static assets.
	 * Defaults to resources/static.
	 */
	public withStaticAssets(publicDir: string): this {
		this.publicDir = publicDir;
		return this;
	}

	/**
	 * Defines the directory in which the assets will be generated.
	 * Defaults to public/build.
	 */
	public withOutput(outDir: string): this {
		this.build!.outDir = outDir;
		return this;
	}

	/**
	 * Adds an entry point.
	 *
	 * @example
	 * export default createViteConfiguration()
	 *  .withEntry("resources/js/app.js")
	 */
	public withEntry(...entries: string[]): this {
		this.build!.rollupOptions!.input = [
			...(this.build!.rollupOptions!.input as string[]),
			...entries,
		];

		return this;
	}

	/**
	 * Adds entry points.
	 *
	 * @example
	 * export default createViteConfiguration()
	 *  .withEntries("resources/js/app.js", "resources/js/admin.js")
	 */
	public withEntries(...entries: string[]): this {
		return this.withEntry(...entries);
	}

	/**
	 * Merges in the given Vite configuration.
	 */
	public merge(config: UserConfig): this {
		const result = deepmerge(this, config);

		for (const [key, value] of Object.entries(result)) {
			// @ts-expect-error
			this[key] = value;
		}

		return this;
	}
}

interface PhpConfiguration {
	build_path: string;
	dev_url: string;
}

/**
 * Gets the configuration from this package's artisan command.
 */
function getConfigurationFromArtisan(): PhpConfiguration | undefined {
	try {
		const { stdout } = execa.sync("php", ["artisan", "vite:config"]);
		return JSON.parse(stdout) as PhpConfiguration;
	} catch (error) {
		console.warn("Could not read configuration from PHP.");
		console.error(error);
	}
}

/**
 * Creates a Vite configuration object, simplified for use with
 * Laravel.
 *
 * @see https://github.com/innocenzi/laravel-vite
 */
export function createViteConfiguration() {
	return new ViteConfiguration(getConfigurationFromArtisan());
}
