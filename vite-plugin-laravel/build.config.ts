import { defineBuildConfig } from 'unbuild'

export default defineBuildConfig({
	entries: [
		'src/index',
		'src/inertia',
	],
	clean: true,
	declaration: true,
	externals: [
		'rollup',
		'vite',
		'esbuild',
		'tailwindcss',
		'autoprefixer',
		'chalk',
		'execa',
	],
	rollup: {
		emitCJS: true,
		cjsBridge: true,
		// inlineDependencies: true,
	},
})
