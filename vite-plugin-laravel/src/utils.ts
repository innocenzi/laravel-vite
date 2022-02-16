import { execaSync } from 'execa'
import { loadEnv } from 'vite'
import { PhpFinderOptions } from './types'

export function parseUrl(urlString: string) {
	return new URL(urlString)
}

export function finish(str: string | undefined, character: string, _default: string = ''): string {
	if (!str) {
		return _default
	}

	if (!str.endsWith(character)) {
		return str + character
	}

	return str
}

export function wrap<T>(input: undefined | T | T[], _default: T[]): T[] {
	if (!input) {
		return _default
	}

	if (Array.isArray(input)) {
		return input
	}

	return [input]
}

/**
 * Finds the path to PHP.
 */
export function findPhpPath(options: PhpFinderOptions = {}): string {
	if (options.path) {
		return options.path
	}

	if (!options.env) {
		options.env = loadEnv(options.mode ?? process.env.NODE_ENV ?? 'development', process.cwd(), '')
	}

	return options.env.PHP_EXECUTABLE_PATH || 'php'
}

/**
 * Calls an artisan command.
 */
export function callArtisan(executable: string, ...params: string[]): string {
	if (process.env.VITEST) {
		return execaSync(process.env.TEST_ARTISAN_SCRIPT!, [executable, 'artisan', ...params], { encoding: 'utf-8' })?.stdout
	}

	return execaSync(executable, ['artisan', ...params])?.stdout
}

/**
 * Calls a shell command.
 */
export function callShell(executable: string, ...params: string[]): string {
	if (process.env.VITEST) {
		return execaSync(process.env.TEST_ARTISAN_SCRIPT!, [executable, ...params])?.stdout
	}

	return execaSync(executable, [...params])?.stdout
}
