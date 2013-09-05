# Garoevans Composer Sym [![Build Status](https://travis-ci.org/garoevans/composer-sym.png)](https://travis-ci.org/garoevans/composer-sym)

Current version: 0.7.11

## Usage

Run;

`./vendor/bin/composer-sym link`

from your projects root.

Other methods;
- link: Loop through your composer.json and see what we can link for you.
- unlink: Give you the option of unlinking any packages we've linked.
- help: List of methods and params.
- getGuessedProjectDir: What we think your project directory is.
- getGuessedHomeDir: What we think you home directory is.

Compser Sym assumes a `vendor/package` structure, so if your project is located in `/home/foo/bar` we will assume that `/home` is your home directory (root of all packages).

## Composer

`"require": {
  "garoevans/composer-sym": "0.7.*"
}`

Due to one of the dependencies Composer Sym also requires the minimum stability to be set to dev;

`"minimum-stability": "dev"`
