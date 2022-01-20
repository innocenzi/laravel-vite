import { PluginOption, UserConfig } from 'vite'
import { manifest } from './manifest'
import { config } from './config'
import { blade } from './blade'
import type { Options } from './types'

export function defineConfig(base: UserConfig = {}) {
	return <UserConfig> {
		...base,
		plugins: [
			...base?.plugins as any,
			config(),
			blade(),
			manifest(),
		],
	}
}

export const laravel = (options: Options = {}): PluginOption[] => [
	blade(),
	config(options),
	manifest(),
]

export { manifest, blade, config }

export default laravel
