# Middleware that compresses HTML response bodies

[![Build Status](https://travis-ci.org/WyriHaximus/reactphp-http-middleware-compress-assets.svg?branch=master)](https://travis-ci.org/WyriHaximus/reactphp-http-middleware-compress-assets)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-http-middleware-compress-assets/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-http-middleware-compress-assets)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-http-middleware-compress-assets/downloads.png)](https://packagist.org/packages/WyriHaximus/react-http-middleware-compress-assets)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-middleware-compress-assets/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-middleware-compress-assets/?branch=master)
[![License](https://poser.pugx.org/WyriHaximus/react-http-middleware-compress-assets/license.png)](https://packagist.org/packages/WyriHaximus/react-http-middleware-compress-assets)
[![PHP 7 ready](http://php7ready.timesplinter.ch/WyriHaximus/reactphp-http-middleware-clear-body/badge.svg)](https://travis-ci.org/WyriHaximus/reactphp-http-middleware-clear-body)

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/react-http-middleware-compress-assets
```

This middleware removes the raw body from the request. Best used after the request body has been parsed.

# Usage

```php
$server = new Server([
    /** Other Middleware */
    new CssCompressMiddleware(),
    new JsCompressMiddleware(),
    /** Other Middleware */
]);
```

Optionally an instance of `WyriHaximus\HtmlCompress\Parser` is accepted as first constructor argument. The default uses a very simple but fast compressor that only compresses HTML. The following example goes for the smallest possible size:

```php
use WyriHaximus\HtmlCompress\Factory;

$server = new Server([
    /** Other Middleware */
    new CssCompressMiddleware(Factory::constructSmallest()),
    new JsCompressMiddleware(Factory::constructSmallest()),
    /** Other Middleware */
]);
```

# License

The MIT License (MIT)

Copyright (c) 2018 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
