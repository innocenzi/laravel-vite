import { UserConfig } from "vite";
import deepmerge from "deepmerge";

export class ViteConfiguration {
    public publicDir: string;
    public build: UserConfig["build"];

    constructor(config: UserConfig = {}) {
        this.publicDir = "resources/static";
        this.build = {
            manifest: true,
            outDir: "public/build",
            rollupOptions: {
                input: [],
            },
        };

        this.merge(config);
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
        this.build.outDir = outDir;
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
        this.build.rollupOptions.input = [
            ...(this.build.rollupOptions.input as string[]),
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
            this[key] = value;
        }

        return this;
    }
}

/**
 * Creates a Vite configuration object, simplified for use with
 * Laravel.
 *
 * @see https://github.com/innocenzi/laravel-vite
 */
export function createViteConfiguration(config: UserConfig = {}) {
    return new ViteConfiguration(config);
}
