[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Travis Status](https://img.shields.io/travis/phpDocumentor/GraphViz.svg?label=Linux)](https://travis-ci.org/phpDocumentor/GraphViz)
[![Appveyor Status](https://img.shields.io/appveyor/ci/phpDocumentor/GraphViz.svg?label=Windows)](https://ci.appveyor.com/project/phpDocumentor/GraphViz/branch/master)
[![Coveralls Coverage](https://img.shields.io/coveralls/github/phpDocumentor/GraphViz.svg)](https://coveralls.io/github/phpDocumentor/GraphViz?branch=master)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/phpDocumentor/GraphViz.svg)](https://scrutinizer-ci.com/g/phpDocumentor/GraphViz/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/phpDocumentor/GraphViz.svg)](https://scrutinizer-ci.com/g/phpDocumentor/GraphViz/?branch=master)
[![Stable Version](https://img.shields.io/packagist/v/phpDocumentor/GraphViz.svg)](https://packagist.org/packages/phpDocumentor/GraphViz)
[![Unstable Version](https://img.shields.io/packagist/vpre/phpDocumentor/GraphViz.svg)](https://packagist.org/packages/phpDocumentor/GraphViz)


GraphViz
========

GraphViz is a library meant for generating .dot files for GraphViz with a
fluent interface.


### PHPStan extension

This library contains a number of magic methods to set attributes on `Node`, `Graph` and `Edge`
this will result in errors when using the library with checks by PHPStan. For your convenience this
library provides an phpStan extension so your code can be checked correctly by phpstan.

```
includes:
    - vendor/phpdocumentor/graphviz/extension.neon
```
