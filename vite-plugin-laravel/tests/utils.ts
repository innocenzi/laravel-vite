import path from 'node:path'

export const fixture = (filepath: string) => path.resolve(__dirname, '__fixtures__', filepath)
export const artisan = path.resolve(__dirname, 'artisan.js')
