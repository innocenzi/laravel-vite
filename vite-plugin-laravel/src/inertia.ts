/**
 * Resolves a page component.
 */
export async function resolvePageComponent(name: string, pages: Record<string, any>) {
	const path = Object.keys(pages)
		.sort((a, b) => a.length - b.length)
		.find((path) => path.endsWith(`${name.replaceAll('.', '/')}.vue`))

	if (!path) {
		throw new Error(`Page not found: ${name}`)
	}

	return typeof pages[path] === 'function'
		? await pages[path]()
		: pages[path]
}
