import { Plugin, UserConfig } from "vite";
import deepmerge from "deepmerge";
import execa from "execa";
import path from "path";
import fs from "fast-glob";
import chalk from 'chalk';
import dotenv from 'dotenv';

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
				path: '*'
			});
		}
	}
})

export class ViteConfiguration {
	public publicDir: string;
	public build: UserConfig["build"];
	public server: UserConfig["server"];
	public plugins: UserConfig["plugins"];
	public base: UserConfig["base"];
	public resolve: UserConfig["resolve"];

	constructor(config?: PhpConfiguration) {
		dotenv.config();
		this.base = process.env.ASSET_URL ?? '/';
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

		this.plugins = [
			laravel()
		];

		if (config?.aliases) {
			this.resolve = {
				alias: Object.fromEntries(
					Object.entries(config.aliases).map(([alias, directory]) => {
						return [alias, path.join(process.cwd(), directory)];
					})
				)
			};

			generateAliases();
		}

		if (config?.dev_url) {
			const [protocol, host, port] = config.dev_url.split(":");
			this.server = {
				host: host.substr(2),
				https: protocol === "https",
				port: port ? Number(port) : 3000,
			};
		}

		if (config?.entrypoints) {
			const directories = !Array.isArray(config?.entrypoints)
				? [config?.entrypoints]
				: config?.entrypoints;

			const matches = fs.sync(
				directories.map((directory) => `${directory}/*`),
				{
					onlyFiles: true,
					globstar: false,
					dot: false,
				}
			);

			(this.build.rollupOptions!.input! as string[]).push(...matches);
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
		const result: UserConfig = deepmerge(this, config);

		for (const [key, value] of Object.entries(result)) {
			if (key === 'base') {
				console.warn(chalk.yellow.bold('(!) "base" option should not be used with Laravel Vite. Use the "ASSET_URL" environment variable instead.'));
			}

			// @ts-expect-error
			this[key] = value;
		}

		return this;
	}
}

interface PhpConfiguration {
	build_path: string;
	dev_url: string;
	entrypoints: false | string | string[];
	aliases: Record<string, string>;
}

/**
 * Calls an artisan command.
 */
function callArtisan(...params: string[]): string {
	return execa.sync("php", ["artisan", ...params])?.stdout;
}

/**
 * Regenerates the tsconfig.json file with aliases.
 */
function generateAliases() {
	try {
		callArtisan("vite:aliases");
	} catch (error) {
		console.warn("Could not regenerate tsconfig.json.");
		console.error(error);
	}
}

/**
 * Gets the configuration from this package's artisan command.
 */
function getConfigurationFromArtisan(): PhpConfiguration | undefined {
	try {
		return JSON.parse(callArtisan("vite:config")) as PhpConfiguration;
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

export default createViteConfiguration();
