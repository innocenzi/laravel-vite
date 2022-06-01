import { it, expect } from 'vitest'
import { readConfig } from '../src/config'
import { artisan, fixture } from './utils'

it('reads config from the given option object', () => {
	const result = readConfig({
		config: {
			aliases: { '@': 'resources' },
			build_path: 'build',
			dev_server: { url: 'http://localhost:5173' },
			entrypoints: { paths: 'resources/scripts/js' },
		},
	}, process.env)

	expect(result).toMatchSnapshot()
})

it('reads config from the given path', () => {
	const result = readConfig({ config: fixture('config.json') }, process.env)

	expect(result)
		.toMatchSnapshot()
})

it('reads config from the CONFIG_PATH_VITE environment variable', () => {
	process.env.CONFIG_PATH_VITE = fixture('config.json')

	expect(readConfig({}, process.env))
		.toMatchSnapshot()
})

it('does not read config the CONFIG_PATH_VITE if config is set to false', () => {
	process.env.TEST_ARTISAN_SCRIPT = artisan
	process.env.CONFIG_PATH_VITE = fixture('config.json')

	expect(readConfig({ config: false }, process.env))
		.toMatchSnapshot()
})

it('tries to read the specified configuration from php', () => {
	process.env.TEST_ARTISAN_SCRIPT = artisan

	expect(readConfig({ config: 'artisan' }, process.env))
		.toMatchSnapshot()
})

it('throws when the named configuration is not found', () => {
	process.env.TEST_ARTISAN_SCRIPT = artisan

	expect(() => readConfig({ config: 'named-but-not-defined' }, process.env))
		.toThrow('Could not read configuration')
})
