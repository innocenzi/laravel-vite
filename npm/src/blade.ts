import { Plugin } from 'vite'

/**
 * Fully reload the page when a blade file is updated.
 */
export const blade = (): Plugin => ({
	name: 'laravel:blade-reload',
	handleHotUpdate({ file, server }) {
		if (['.blade.php', 'vite.php'].some((name) => file.endsWith(name))) {
			server.ws.send({
				type: 'full-reload',
				path: '*',
			})
		}
	},
})

// server.watcher.add(watchAdditionalPaths)
