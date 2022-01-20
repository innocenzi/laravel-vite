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
