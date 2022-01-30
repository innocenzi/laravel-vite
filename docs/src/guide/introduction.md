---
title: Introduction
editLink: false
---

# Introduction

## Overview

Laravel Vite is a project that aims to integrate [Vite](https://vitejs.dev) as much as possible with [Laravel](https://laravel.com). It consists of three parts:

- A PHP package, which provides support for parsing Vite's [manifest](https://vitejs.dev/guide/backend-integration.html#backend-integration), [Blade directives](/guide/usage#directives), and [helper functions](/guide/usage#directives).
- A Node package, which leverages Laravel's configuration to integrate with Vite's, and provides Blade file hot module replacement support.
- A [preset](https://preset.dev), which installs Vite in a fresh Laravel project with just one command.

## Traditional approach

Laravel comes with [Laravel Mix](https://laravel-mix.com), an abstraction of Webpack. It is a well-known and battle-tested bundler, but Vite takes a different approach to improve the experience of front-end development. You can learn the details on [its documentation](https://vitejs.dev/guide/why.html#the-problems).

That means that Laravel Vite is **a replacement for Laravel Mix**.

## Requirements

- The Laravel package requires PHP 8.0 or greater.
- Vite requires Node 12.0.0 or greater.
- Vite requires [native ESM dynamic import support](https://caniuse.com/es6-module-dynamic-import) during development.
- The production build assumes a baseline support for [native ESM via script tags](https://caniuse.com/es6-module). Vite does not perform any compatibility transpilation by default. Legacy browsers can be supported via the official [@vitejs/plugin-legacy](https://github.com/vitejs/vite/tree/main/packages/plugin-legacy).
