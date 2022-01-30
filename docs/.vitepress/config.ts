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
]

const nav: NavItem[] = [
	{ text: 'Docs', link: '/guide/quick-start' },
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
	'/guide/': [
		{
			text: 'Getting started',
			items: [
				{ text: 'Introduction', link: '/guide/introduction' },
				{ text: 'Quick start', link: '/guide/quick-start' },
			],
		},
		{
			text: 'Essentials',
			items: [
				{ text: 'Development', link: '/guide/essentials/development' },
				{ text: 'Configuration', link: '/guide/essentials/configuration' },
				{ text: 'Building for production', link: '/guide/essentials/building-for-production' },
			],
		},
		{
			text: 'Features',
			items: [
				{ text: 'Server and manifest modes', link: '/guide/features/server-and-manifest-modes' },
				{ text: 'Directives', link: '/guide/features/directives' },
				{ text: 'Helpers', link: '/guide/features/helpers' },
				{ text: 'Entrypoints', link: '/guide/features/entrypoints' },
				{ text: 'SSR', link: '/guide/features/ssr' },
			],
		},
		{
			text: 'Guides',
			items: [
				{ text: 'Inertia', link: '/guide/inertia' },
				{ text: 'Multiple configurations', link: '/guide/multiple-configurations' },
			],
		},
		{
			text: 'Extra topics',
			items: [
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

		// https://github.com/vuejs/theme/pull/44
		// editLink: {
		// 	repo: 'innocenzi/laravel-vite',
		// 	text: 'Edit this page on Github',
		// },

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
