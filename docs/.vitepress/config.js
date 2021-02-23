// @ts-check
// Credits to https://github.com/ElMassimo/vite_ruby/blob/main/docs/.vitepress/config.js

const production = process.env.NODE_ENV === 'production'
const title = 'Laravel Vite'
const description = 'Vite integration for the Laravel framework'
const site = production ? 'https:/laravel-vite.netlify.app' : 'http://localhost:3000'
const image = `${site}/banner.png`
const head = [
	// ['style', {}, 'img { border-radius: 10px }' + 'h1.title { margin-left: 0.5em }'],
	['meta', { name: 'author', content: 'Enzo Innocenzi' }],
	['meta', { name: 'keywords', content: 'laravel, vitejs, vue, react, vite, php' }],
	['link', { rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg' }],
	['meta', { name: 'HandheldFriendly', content: 'True' }],
	['meta', { name: 'MobileOptimized', content: '320' }],
	['meta', { name: 'theme-color', content: '#f62d1c' }],
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

if (production) {
	// @ts-ignore
	head.push(['script', { src: 'https://unpkg.com/thesemetrics@latest', async: '' }])
}

/**
 * @type {import('vitepress').UserConfig}
 */
module.exports = {
	title,
	description,
	head,
	themeConfig: {
		repo: 'innocenzi/laravel-vite',
		logo: '/favicon.svg',
		docsDir: 'docs',
		docsBranch: 'main',
		editLinks: true,
		editLinkText: 'Suggest changes to this page',
		nav: [
			{ text: 'Guide', link: '/guide/' },
			{ text: 'Preset', link: 'https://github.com/laravel-presets/vite' },
			{ text: 'Vite', link: 'https://vitejs.dev/' },
		],
		sidebar: {
			'/': [
				{
					text: 'Guide',
					children: [
						{ text: 'Introduction', link: '/guide/introduction' },
						{ text: 'Getting started', link: '/guide/' },
						{ text: 'Usage', link: '/guide/usage' },
						{ text: 'Building for production', link: '/guide/production' },
						{ text: 'Configuration', link: '/guide/configuration' },
					],
				},
			],
		},
	},
}
