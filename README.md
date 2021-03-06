# SEO
 
[![Packagist](https://img.shields.io/packagist/v/icybee/module-seo.svg)](https://packagist.org/packages/icybee/module-seo)
[![Build Status](https://img.shields.io/travis/Icybee/module-seo.svg)](http://travis-ci.org/Icybee/module-seo)
[![Code Quality](https://img.shields.io/scrutinizer/g/Icybee/module-seo.svg)](https://scrutinizer-ci.com/g/Icybee/module-seo)
[![Code Coverage](https://img.shields.io/coveralls/Icybee/module-seo.svg)](https://coveralls.io/r/Icybee/module-seo)
[![Downloads](https://img.shields.io/packagist/dt/icybee/module-seo.svg)](https://packagist.org/packages/icybee/module-seo/stats)





## Event hooks





### `Icybee\Modules\Pages\EditBlock::alter_children`

Adds the controls used to edit the SEO title and description of the page.





### `Icybee\Modules\Pages\ExportOperation::process`

Adds SEO properties to exported pages.





### `Icybee\Modules\Pages\PageController::render`


Adds the Google Analytics script at the end of the body, unless one of the following
conditions is met:

- "localhost" is in the server name.
- The user is the admin.
- The page or the displayed record is offline.





### `Icybee\Modules\Sites\EditBlock::alter_children`

Extends the site edit block with a `SEO` group and controls for the Google Analytics UA
and the Google Site Verification key.





### `Brickrouge\Document::render_title:before`

Replaces the title of the document with the SEO title before the title is rendered.





### `Brickrouge\Document::render_metas:before`

Adds the `Description` and `google-site-verification` metas.





### `Brickrouge\Document::render_metas`

Adds the canonical address of the document.





----------





## Requirement

The package requires PHP 5.4 or later.  
The package also requires an installation of [Icybee](http://icybee.org).





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/).
Create a `composer.json` file and run `php composer.phar install` command to install it:

	$ composer require icybee/module-seo





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-seo), its repository can be
cloned with the following command line:

	$ git clone git://github.com/Icybee/module-seo.git images





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/Icybee/module-seo.svg)](http://travis-ci.org/Icybee/module-seo)
[![Code Coverage](https://img.shields.io/coveralls/Icybee/module-seo.svg)](https://coveralls.io/r/Icybee/module-seo)





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.





## License

The module is licensed under the New BSD License - See the LICENSE file for details.
