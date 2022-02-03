import path from 'node:path'
import { expect, it } from 'vitest'
import { build } from 'vite'
import laravel from '../src'

it.only('runs the plugin as expected', async() => {
	const result = await build({
		root: path.resolve(__dirname, '__fixtures__'),
		logLevel: 'silent',
		plugins: [
			laravel({
				config: {
					aliases: { '@': 'vite-plugin-laravel/tests/__fixtures__/resources' },
					build_path: 'build',
					dev_server: { url: 'http://localhost:3000' },
					entrypoints: { paths: 'resources/scripts/app.ts' },
				},
			}),
		],
	})

	expect(result).toMatchSnapshot()
})
