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
	// ['style', {}, 'img { border-radius: 10px }' + 'h1.title { margin-left: 0.5em }'],
	['meta', { name: 'author', content: 'Enzo Innocenzi' }],
	['meta', { name: 'keywords', content: 'laravel, vitejs, vue, react, vite, php' }],
	['link', { rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg' }],
	['meta', { name: 'HandheldFriendly', content: 'True' }],
	['meta', { name: 'MobileOptimized', content: '320' }],
	['meta', { name: 'theme-color', content: '#6a9dff' }],
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
]

const nav: NavItem[] = [
	{ text: 'Guide', link: '/guide/' },
	{ text: 'Vite', link: 'https://vitejs.dev/' },
]

const sidebar: SidebarConfig = {
	'/': [
		{
			text: 'Guide',
			items: [
				{ text: 'Introduction', link: '/guide/introduction' },
				{ text: 'Quick start', link: '/guide/quick-start' },
			],
		},
		{
			text: 'Essentials',
			items: [
				{ text: 'Development', link: '/guide/essentials/development' },
				{ text: 'Static assets', link: '/guide/essentials/static-assets' },
				{ text: 'Configuration', link: '/guide/essentials/configuration' },
			],
		},
		{
			text: 'Features',
			items: [
				{ text: 'Server and manifest modes', link: '/guide/features/server-and-manifest-modes' },
				{ text: 'Directives', link: '/guide/features/directives' },
				{ text: 'Helpers', link: '/guide/features/helpers' },
				{ text: 'Entrypoints', link: '/guide/features/entrypoints' },
				{ text: 'Multiple configurations', link: '/guide/features/multiple-configurations' },
				{ text: 'SSR', link: '/guide/features/ssr' },
			],
		},
		{
			text: 'Configuration',
			items: [
				{ text: 'Laravel', link: '/guide/configuration/laravel' },
				{ text: 'Vite', link: '/guide/configuration/vite' },
			],
		},
		{
			text: 'Extra topics',
			items: [
				{ text: 'Building for production', link: '/guide/extra-topics/building-for-production' },
				{ text: 'Inertia', link: '/guide/extra-topics/inertia' },
				{ text: 'Organization', link: '/guide/extra-topics/organization' },
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
		],

		editLink: {
			repo: 'innocenzi/laravel-vite',
			text: 'Edit this page on Github',
		},

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
	},
})
