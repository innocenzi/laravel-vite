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
