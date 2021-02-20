import path from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
	build: {
		lib: {
			entry: path.resolve(__dirname, 'index.ts'),
			name: 'laravel-vite',
			formats: ['es'],
		},
	},
})
