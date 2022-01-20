import type { Options } from 'tsup'

export const tsup: Options = {
	clean: true,
	dts: true,
	format: ['cjs'],
	external: ['rollup', 'vite', 'esbuild', 'tailwindcss', 'autoprefixer'],
	noExternal: ['execa'],
}
