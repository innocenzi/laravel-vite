import type { Options } from 'tsup'

export const tsup: Options = {
	clean: true,
	dts: true,
	format: ['cjs', 'esm'],
	external: ['rollup', 'vite', 'esbuild', 'tailwindcss', 'autoprefixer'],
	noExternal: ['execa'],
}
