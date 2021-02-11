---
title: Getting started
editLink: true
---

# Getting started

## Compatibility

- This package requires PHP version 8.0.0 or higher.
- [Vite](htts://vitejs.dev) requires [Node](https://nodejs.org/en/) version 12.0.0 or higher.

## Installation

### Via preset

Use the following command:

```bash
npx apply laravel:vite
```

> If you're on a Mac, you may need to use the `--ignore-existing` flag.

### Manually

First, require the package:

```bash
$ composer require innocenzi/laravel-vite
```

Then, install the NPM packages:

```bash
$ yarn add vite laravel-vite --dev
```

Create a `vite.config.ts` file at the root of your project, with the following content:

```ts
// vite.config.ts
import { createViteConfiguration } from "laravel-vite";

export default createViteConfiguration();
```

Edit your `package.json` file to add these scripts:

```json
{
	"scripts": {
		"dev": "vite",
		"build": "vite build",
		"serve": "vite preview"
	}
}
```

Lastly, if Laravel Mix is installed, you can get rid of it. Remove `webpack.mix.js` and the dependency on `laravel-mix`.

## Usage

In your Blade files, add the `@vite` directive to include the Vite client and enable hot module replacement.
Add a `@vite` directive for each entry point.

For instance, if you have:

```
/resources
  /scripts
    /main.ts
```

You can use `@vite('main')`. If the script is not in one of the entrypoint directories, you'll need to enter the full path: `@vite('resources/js/app.js')`.
