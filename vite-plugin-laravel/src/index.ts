import { PluginOption, UserConfig } from 'vite'
import { manifest } from './manifest'
import { config } from './config'
import { reload } from './reload'
import { callArtisan, callShell, findPhpPath } from './utils'
import type { Options } from './types'

export function defineConfig(base: UserConfig = {}) {
	return <UserConfig> {
		...base,
		plugins: [
			...base?.plugins as any,
			config(),
			reload(),
			manifest(),
		],
	}
}

export const laravel = (options: Options = {}): PluginOption[] => [
	reload(options),
	config(options),
	manifest(),
]

export * from './inertia'
export { manifest, reload, config, callArtisan, callShell, findPhpPath, Options }
export default laravel
