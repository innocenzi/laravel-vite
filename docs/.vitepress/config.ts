import baseConfig from '@vue/theme/config'
import { defineConfigWithTheme, HeadConfig, UserConfig } from 'vitepress'
import type { Config } from '@vue/theme'
import { NavItem, SidebarConfig } from '@vue/theme/src/vitepress/config'

const production = process.env.NODE_ENV === 'production'
const title = 'Laravel Vite'
const description = 'Vite integration for the Laravel framework'
const site = production ? 'https://laravel-vite.innocenzi.dev' : 'http://localhost:3000'
const image = `${site}/banner.png`

const head: HeadConfig[] = [
	['meta', { name: 'author', content: 'Enzo Innocenzi' }],
	['meta', { name: 'keywords', content: 'laravel, vitejs, vue, react, vite, php' }],
	['link', { rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg' }],
	['meta', { name: 'HandheldFriendly', content: 'True' }],
	['meta', { name: 'MobileOptimized', content: '320' }],
	['meta', { name: 'theme-color', content: '#d8b4fe' }],
	['meta', { name: 'twitter:card', content: 'summary_large_image' }],
	['meta', { name: 'twitter:site', content: site }],
	['meta', { name: 'twitter:title', value: title }],
	['meta', { name: 'twitter:description', value: description }],
	['meta', { name: 'twitter:image', content: image }],
	['meta', { property: 'og:type', content: 'website' }],
	['meta', { property: 'og:locale', content: 'en_US' }],
	['meta', { property: 'og:site', content: site }],
	['meta', { property: 'og:site_name', content: title }],
	['meta', { property: 'og:title', content: title }],
	['meta', { property: 'og:image', content: image }],
	['meta', { property: 'og:description', content: description }],
	['meta', { name: 'robots', content: 'noindex' }],
]

const nav: NavItem[] = [
	{ text: 'Docs', link: '/guide/quick-start' },
	{
		text: 'Config',
		items: [
			{ text: 'Laravel package', link: '/configuration/laravel-package' },
			{ text: 'Vite plugin', link: '/configuration/vite-plugin' },
		],
	},
	{
		text: 'Links',
		items: [
			{
				text: 'Presets',
				items: [
					{ text: 'Laravel Vite', link: 'https://github.com/laravel-presets/vite' },
					{ text: 'Laravel Inertia', link: 'https://github.com/laravel-presets/inertia' },
				],
			},
			{
				text: 'Documentation',
				items: [
					{ text: 'Vite', link: 'https://vitejs.dev/' },
					{ text: 'Preset', link: 'https://preset.dev/' },
					{ text: 'Laravel', link: 'https://laravel.com/' },
				],
			},
		],
	},
]

const sidebar: SidebarConfig = {
	'/configuration/': [
		{
			text: 'Laravel package',
			items: [
				{ text: 'configs', link: '/configuration/laravel-package#configs' },
				{ text: 'aliases', link: '/configuration/laravel-package#aliases' },
				{ text: 'commands', link: '/configuration/laravel-package#commands' },
				{ text: 'testing', link: '/configuration/laravel-package#testing' },
				{ text: 'env_prefixes', link: '/configuration/laravel-package#env-prefixes' },
				{ text: 'interfaces', link: '/configuration/laravel-package#interfaces' },
				{ text: 'default', link: '/configuration/laravel-package#default' },
			],
		},
		{
			text: 'Vite plugin',
			items: [
				{ text: 'config', link: '/configuration/vite-plugin#config' },
				{ text: 'php', link: '/configuration/vite-plugin#php' },
				{ text: 'postcss', link: '/configuration/vite-plugin#postcss' },
				{ text: 'ssr', link: '/configuration/vite-plugin#ssr' },
				{ text: 'updateTsConfig', link: '/configuration/vite-plugin#updateTsConfig' },
				{ text: 'allowOverrides', link: '/configuration/vite-plugin#allowOverrides' },
				{ text: 'watch', link: '/configuration/vite-plugin#watch' },
			],
		},
	],
	'/guide/': [
		{
			text: 'Getting started',
			items: [
				{ text: 'Introduction', link: '/guide/introduction' },
				{ text: 'Quick start', link: '/guide/quick-start' },
				{ text: 'Upgrade guide', link: '/guide/upgrade' },
			],
		},
		{
			text: 'Essentials',
			items: [
				{ text: 'Development', link: '/guide/essentials/development' },
				{ text: 'Server and manifest modes', link: '/guide/essentials/server-and-manifest-modes' },
				{ text: 'Working with assets', link: '/guide/essentials/working-with-assets' },
				{ text: 'Configuration', link: '/guide/essentials/configuration' },
				{ text: 'Building for production', link: '/guide/essentials/building-for-production' },
				{ text: 'Tag generation', link: '/guide/essentials/tag-generation' },
			],
		},
		{
			text: 'Features',
			items: [
				{ text: 'Entrypoints', link: '/guide/features/entrypoints' },
				{ text: 'Directives', link: '/guide/features/directives' },
				{ text: 'Helpers', link: '/guide/features/helpers' },
				{ text: 'SSR', link: '/guide/features/ssr' },
			],
		},
		{
			text: 'Extra topics',
			items: [
				{ text: 'Vite with Inertia', link: '/guide/extra-topics/inertia' },
				{ text: 'Multiple configurations', link: '/guide/extra-topics/multiple-configurations' },
				{ text: 'Path to PHP', link: '/guide/extra-topics/php-path' },
				{ text: 'Troubleshooting', link: '/guide/extra-topics/troubleshooting' },
			],
		},
	],
}

export default defineConfigWithTheme<Config>({
	extends: baseConfig as () => UserConfig<Config>,
	title,
	head,
	description,
	lang: 'en-US',
	scrollOffset: 'header',
	srcDir: 'src',

	themeConfig: {
		nav,
		sidebar,

		algolia: {
			appId: 'UNQJXGJJCM',
			apiKey: '13f1ef823ef6da38d5b51452d5768113',
			indexName: 'laravel-vite',
		},

		socialLinks: [
			{ icon: 'github', link: 'https://github.com/innocenzi/laravel-vite' },
			{ icon: 'twitter', link: 'https://twitter.com/enzoinnocenzi' },
			{ icon: 'discord', link: 'https://chat.vitejs.dev' },
		],

		footer: {
			license: {
				text: 'MIT License',
				link: 'https://opensource.org/licenses/MIT',
			},
			copyright: 'Made with ❤️ by Enzo Innocenzi',
		},
	},

	vite: {
		server: {
			host: true,
			fs: {
				allow: ['../..'],
			},
		},
		build: {
			minify: 'terser',
			chunkSizeWarningLimit: Infinity,
			rollupOptions: {
				output: {
					chunkFileNames: 'assets/chunks/[name].[hash].js',
					manualChunks: (id, ctx) => moveToVendor(id, ctx),
				},
			},
		},
		json: {
			stringify: true,
		},
	},
})

const cache = new Map<string, boolean>()

/**
 * This is temporarily copied from Vite - which should be exported in a
 * future release.
 *
 * @TODO when this is exported by Vite, VitePress should ship a better
 * manual chunk strategy to split chunks for deps that are imported by
 * multiple pages but not all.
 */
function moveToVendor(id: string, { getModuleInfo }: any) {
	if (
		id.includes('node_modules')
    && !/\.css($|\\?)/.test(id)
    && staticImportedByEntry(id, getModuleInfo, cache)
	) {
		return 'vendor'
	}
}

function staticImportedByEntry(
	id: string,
	getModuleInfo: any,
	cache: Map<string, boolean>,
	importStack: string[] = [],
): boolean {
	if (cache.has(id)) {
		return cache.get(id) as boolean
	}
	if (importStack.includes(id)) {
		// circular deps!
		cache.set(id, false)

		return false
	}
	const mod = getModuleInfo(id)
	if (!mod) {
		cache.set(id, false)

		return false
	}

	if (mod.isEntry) {
		cache.set(id, true)

		return true
	}
	const someImporterIs = mod.importers.some((importer: string) =>
		staticImportedByEntry(
			importer,
			getModuleInfo,
			cache,
			importStack.concat(id),
		),
	)
	cache.set(id, someImporterIs)

	return someImporterIs
}
