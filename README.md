# MachO

PHP script for modifying [Mach-O](https://en.wikipedia.org/wiki/Mach-O) 64-bit executable x86_64 files using PHP.

## Project Outlines

The project outlines as described in my blog post about [Open Source Software Collaboration](https://blog.fox21.at/2019/02/21/open-source-software-collaboration.html).

- The main purpose of this software is to modify Mach-O 64-bit executable x86_64 files using PHP.
- This list is open. Feel free to request features.

## Installation

The preferred method of installation is via [Packagist](https://packagist.org/packages/thefox/macho) and [Composer](https://getcomposer.org/). Run the following command to install the package and add it as a requirement to composer.json:

```bash
composer require thefox/macho
```

## Usage

To print general informations about a binary executable you can type the following command into your shell:

```bash
./macho binary --all PATH
```

For example:

```bash
./macho binary --all
```

Another way to print general informations about a binary executable is to use `dbg.sh` script. This script uses default OS X tools like `nm` and `otool`.

## Links

- [Blog Post about Mach-O project](http://blog.fox21.at/2015/02/14/mach-o.html)
- [Packagist Package](https://packagist.org/packages/thefox/macho)

## Examples

Look into [examples](examples) directory to see code examples how to use this in your own project.
