# Netki PHP Partner Library

This is the Netki Partner library written in PHP. It allows you to use the Netki Partner API to CRUD all of your partner data:

* Wallet Names
* Domains
* Partners

## Requirements

PHP 5.4 and later.

## Dependencies

If you manually install, ensure you resolve the following dependencies 

* [rmccue/requests](https://github.com/rmccue/Requests)

## Composer

```php
composer require netki/netki-partner-client
```

Simply use autoload to avail the client to your project.

```php
require_once('vendor/autoload.php');
```

## Manual Installation

You can clone this repo if you prefer not to use [Composer](https://getcomposer.org). Simply include the `init.php` in your project.
 
```php
require_once('/path/to/netki/php-partner-client/init.php')
```

## Documentation
Detailed API documentation can be found at [Netki's Apiary](http://docs.netki.apiary.io/).


## Example

See `example/example.php` for detailed usage