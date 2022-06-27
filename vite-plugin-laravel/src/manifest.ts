import fs from 'node:fs/promises'
import path from 'node:path'
import { createHash } from 'node:crypto'
import { Manifest, ManifestChunk, Plugin, ResolvedConfig } from 'vite'

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
	const manifest = new Map<string, ManifestChunk>()

	let config: ResolvedConfig

	return {
		name: PREFIX,
		apply: 'build',
		enforce: 'post',

		configResolved(resolved) {
			config = resolved
		},

		async generateBundle(_, bundle) {
			const entrypoints = getEntrypoints(config)

			if (!entrypoints) {
				return
			}

			const values = Object.values(bundle)
			const assets = values.filter((c) => c.type === 'asset') //  as OutputAsset[] // from rollup

			// -- CSS

			const cssEntrypoints = entrypoints.filter((entry) => isStylesheet(entry))
			const cssAssets = assets.filter((asset) => isStylesheet(asset.name!))

			if (config.build.cssCodeSplit) {
				// add CSS entrypoints to manifest
				for (const chunk of cssAssets) {
					if (!chunk.name) {
						continue
					}
					const name = removeExtension(chunk.name!)
					for (const entry of cssEntrypoints) {
						if (removeExtension(path.basename(entry)) === name) {
							manifest.set(entry, { file: chunk.fileName, src: entry, isEntry: true })
						}
					}
				}
			} else {
				// Vite emits a single CSS file in this mode, named `style.css`
				const chunk = assets.find((asset) => asset.name === 'style.css')
				if (chunk) {
					manifest.set(chunk.name!, { file: chunk.fileName, src: chunk.name! })
				}
			}

			// -- Remaining Assets

			const remaining = entrypoints.filter((entry) => isAssetEntrypoint(entry))

			for (const entry of remaining) {
				const fullPath = path.join(config.root, entry)
				const source = await fs.readFile(fullPath)
				const hash = getAssetHash(source)

				const ext = path.extname(entry)
				const name = removeExtension(entry)
				const fileName = path.posix.join(
					config.build.assetsDir,
					`${path.basename(name)}.${hash}${ext}`,
				)

				manifest.set(entry, { file: fileName, src: entry, isEntry: true })

				if (!bundle[fileName]) {
					this.emitFile({ name: entry, fileName, source, type: 'asset' })
				}
			}
		},

		async writeBundle(_opts, bundle) {
			if (!bundle['manifest.json']) {
				return
			}

			// this is where the patching takes place,
			// we find the manifest, read it, and then merge it with our own.
			// once done, we write it back to disk

			const manifestPath = path.resolve(config.root, config.build.outDir, 'manifest.json')
			const viteManifest = JSON.parse(await fs.readFile(manifestPath, 'utf8')) as Manifest

			for (const [key, value] of Object.entries(viteManifest)) {
				manifest.set(key, value)
			}

			await fs.writeFile(
				manifestPath,
				JSON.stringify(Object.fromEntries(manifest), null, 2),
			)
		},
	}
}

/**
 * Gets the entrypoints from Rollup configuration.
 * Entrypoints will be relative to the config root.
 */
function getEntrypoints(config: ResolvedConfig) {
	let input = config.build.rollupOptions.input

	if (!input) {
		return null
	}

	if (typeof input === 'string') {
		input = [input]
	}

	if (typeof input === 'object' && !Array.isArray(input)) {
		const keys = Object.keys(input)
		if (keys.length === 0) {
			return null
		}
		input = keys
	}

	if (input.length === 0) {
		return null
	}

	return input.map((entry) => path.relative(config.root, entry).replaceAll('\\', '/'))
}

/**
 * Removes the extension from a filename.
 */
function removeExtension(filename: string) {
	return filename.replace(/\.[^.]*$/, '')
}

/**
 * Determines if a filename is a stylesheet, e.g. `.css` or `.scss`.
 */
function isStylesheet(filename: string) {
	return /\.(css|less|sass|scss|styl|stylus|pcss|postcss)$/.test(filename)
}

/**
 * Determines if a filename is an asset entrypoint,
 * which is an entrypoint otherwise not supported by Vite.
 * This is pretty much non `html`, `js` and `css` files.
 */
function isAssetEntrypoint(filename: string) {
	if (isStylesheet(filename)) {
		return false
	}

	return !/\.(html|jsx?|tsx?)$/.test(filename)
}

/**
 * Gets the asset hash for the contents of a file, the same way Vite does it.
 */
function getAssetHash(content: Buffer) {
	return createHash('sha256').update(content).digest('hex').slice(0, 8)
}
