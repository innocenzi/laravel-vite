import fs from 'node:fs'
import path from 'node:path'
import makeDebugger from 'debug'

const debug = makeDebugger('vite:laravel:alias')
const tsconfigPath = path.resolve('tsconfig.json')

export function updateAliases(aliasConfig: Record<string, string>) {
	if (!fs.existsSync(tsconfigPath)) {
		debug('tsconfig.json do not exist, creating it.')
		writeTsConfig()
	}

	const file = fs.readFileSync(tsconfigPath, { encoding: 'utf-8' })
	const indent = file.split('\n').at(1)?.split('"').at(0) ?? 2
	const tsconfig = JSON.parse(file)
	const aliases = Object.fromEntries(Object.entries(aliasConfig).map(([key, value]) => [
		`${key}/*`,
		[`${value}/*`],
	]))

	tsconfig.compilerOptions.paths = {
		...tsconfig.compilerOptions.paths,
		...aliases,
	}

	debug('Previous:', tsconfig.compilerOptions.paths)
	debug('New:', aliases)
	debug('Indent:', `'${indent}'`)

	if (JSON.stringify(tsconfig.compilerOptions.paths) === JSON.stringify(aliases)) {
		debug('Aliases are the same, skipping.')

		return
	}

	debug('Writing aliases.')
	fs.writeFileSync(tsconfigPath, JSON.stringify(tsconfig, null, indent))
}

export function writeTsConfig() {
	fs.writeFileSync(tsconfigPath, JSON.stringify({
		compilerOptions: {
			target: 'esnext',
			module: 'esnext',
			moduleResolution: 'node',
			strict: true,
			jsx: 'preserve',
			sourceMap: true,
			resolveJsonModule: true,
			esModuleInterop: true,
			lib: [
				'esnext',
				'dom',
			],
			types: [
				'vite/client',
			],
			baseUrl: '.',
			paths: {
				'@/*': [
					'resources/*',
				],
			},
		},
		include: [
			'resources/**/*',
		],
	}, null, 2))
}
