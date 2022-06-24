import fs from 'node:fs'
import path from 'node:path'
import { normalizePath } from 'vite'
import type { Plugin, ResolvedConfig, Manifest } from 'vite'

const PREFIX = 'vite:laravel:config'

// The concept of this plugin was taken from the Vite Ruby project.
// See: https://github.com/ElMassimo/vite_ruby/blob/main/vite-plugin-ruby/src/manifest.ts
// The main difference is that instead of writing a separate manifest,
// this plugin merges its manifest with the one from Vite.

/**
 * Creates a plugin for patching Vite's manifest file.
 * @see https://github.com/innocenzi/laravel-vite/issues/153
 */
export function manifest(): Plugin {
	let config: ResolvedConfig
	const cssManifest: Manifest = {}

	return {
		name: PREFIX,
		apply: 'build',
		enforce: 'post',

		configResolved(resolved) {
			config = resolved
		},

		// The following two hooks add CSS entry points into the manifest because Vite does not currently do this.
		// Both shamelessly stolen from https://github.com/laravel/vite-plugin/blob/main/src/index.ts
		renderChunk(_, chunk) {
			const cssLangs = '\\.(css|less|sass|scss|styl|stylus|pcss|postcss)($|\\?)'
			const cssLangRE = new RegExp(cssLangs)

			if (!chunk.isEntry || chunk.facadeModuleId === null || !cssLangRE.test(chunk.facadeModuleId)) {
				return null
			}

			const relativeChunkPath = normalizePath(path.relative(config.root, chunk.facadeModuleId))

			cssManifest[relativeChunkPath] = {
				/* @ts-ignore */
				file: Array.from(chunk.viteMetadata.importedCss)[0],
				src: relativeChunkPath,
				isEntry: true,
			}

			return null
		},
		writeBundle() {
			const manifestConfig = resolveManifestConfig(config)

			if (manifestConfig === false) {
				return
			}

			const manifestPath = path.resolve(config.root, config.build.outDir, manifestConfig)
			const manifest = JSON.parse(fs.readFileSync(manifestPath).toString())
			const newManifest = {
				...manifest,
				...cssManifest,
			}
			fs.writeFileSync(manifestPath, JSON.stringify(newManifest, null, 2))
		},
	}
}

/**
 * Resolve the Vite manifest config from the configuration.
 */
function resolveManifestConfig(config: ResolvedConfig): string | false {
	const manifestConfig = config.build.ssr
		? config.build.ssrManifest
		: config.build.manifest

	if (manifestConfig === false) {
		return false
	}

	if (manifestConfig === true) {
		return config.build.ssr ? 'ssr-manifest.json' : 'manifest.json'
	}

	return manifestConfig
}
