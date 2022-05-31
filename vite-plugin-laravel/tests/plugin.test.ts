import path from 'node:path'
import { expect, it } from 'vitest'
import { build } from 'vite'
import laravel from '../src'

it('runs the plugin as expected', async() => {
	const result = await build({
		root: path.resolve(__dirname),
		logLevel: 'silent',
		plugins: [
			laravel({
				config: {
					aliases: { '@': 'vite-plugin-laravel/tests/__fixtures__/resources' },
					build_path: 'build',
					dev_server: { url: 'http://localhost:3000' },
					entrypoints: { paths: path.resolve(__dirname, './__fixtures__/resources/scripts/app.ts') },
				},
			}),
		],
	}) as any

	expect(result.output[0].fileName).to.match(/assets\/app\..*\.js/)
	expect(result.output[0]).toMatchObject({
		code: 'console.log("app content");\n',
		isDynamicEntry: false,
		isEntry: true,
		isImplicitEntry: false,
		type: 'chunk',
	})

	expect(result.output[1].fileName).to.match(/assets\/app\..*\.css/)
	expect(result.output[1]).toMatchObject({
		name: 'app.css',
		fileName: 'assets/app.59db6eb8.css',
		source: 'body{color:red}\n',
		isAsset: true,
		type: 'asset',
	})
})
