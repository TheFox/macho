# MachO
PHP script for modifying [Mach-O](https://en.wikipedia.org/wiki/Mach-O) 64-bit executable x86_64 files.

## Installation
The preferred method of installation is via [Packagist](https://packagist.org/packages/thefox/macho) and [Composer](https://getcomposer.org/). Run the following command to install the package and add it as a requirement to composer.json:

	composer.phar require "thefox/macho=~0.1"

## Usage
- To print general informations about a binary executable you can type the following command into your shell:
	
		./application.php binary --all ~/work/dev/test2/build/test1
- Another way to print general informations about a binary executable is to use `dbg.sh` script. This script uses default OS X tools like `nm` and `otool`.

## Related Links
- <https://developer.apple.com/library/mac/documentation/DeveloperTools/Conceptual/MachORuntime/index.html>

## License
Copyright (C) 2014 - 2015 Christian Mayer <http://fox21.at>

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
