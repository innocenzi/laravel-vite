import { Plugin, ViteDevServer } from 'vite'
import makeDebugger from 'debug'
import { Options, WatchOptions } from './types'

const PREFIX = 'vite:laravel:reload'
const debug = makeDebugger(PREFIX)

/**
 * Reload when some files are changed.
 */
export const reload = (options: Options = {}): Plugin => {
	const watchOptions: Required<WatchOptions> = {
		input: [],
		reloadOnBladeUpdates: true,
		reloadOnConfigUpdates: true,
		...(Array.isArray(options.watch) ? { input: options.watch } : options.watch),
	}

	debug('Given options:', options)
	debug('Resolved options:', watchOptions)

	// When the config change, we want a full module graph
	// invalidation as well as a full reload
	watchOptions.input.push({
		condition: (file) => file.endsWith('config/vite.php'),
		handle: ({ server }) => {
			debug('Configuration file changed, invalidating module graph and reloading')
			server.moduleGraph.invalidateAll()
			server.ws.send({ type: 'full-reload', path: '*' })
		},
	})

	// Blade files
	watchOptions.input.push({
		condition: (file) => file.endsWith('.blade.php'),
		handle: ({ server }) => {
			debug('Blade file changed, reloading')
			server.ws.send({ type: 'full-reload', path: '*' })
		},
	})

	function handleReload(file: string, server: ViteDevServer) {
		file = file.replaceAll('\\', '/')

		watchOptions.input.forEach((value) => {
			if (value.condition(file)) {
				debug(`${file} changed, applying its handler`)
				value.handle({ file, server })
			}
		})
	}

	return {
		name: 'vite:laravel:reload',
		configureServer(server) {
			server.watcher
				.on('add', (path) => handleReload(path, server))
				.on('change', (path) => handleReload(path, server))
				.on('unlink', (path) => handleReload(path, server))
		},
	}
}
