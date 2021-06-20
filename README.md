# Essentials for testing Magento 2 modules

Using mocking frameworks for testing Magento 2 modules is counterproductive as you replicate line by line your actual calls to a magento implementation.

Binding your tests to the details of implementation leads to very fragile test suite that no-one wants to work with afterwards as a small change in underlying code like extraction of the class requires complete rewrite of the test case. 

This package solves this problem by providing `fake objects` for most common operations you might want to interact with core.

As well as set of fake objects there is an  `ObjectManagerInterface` implementation that automatically instantiates dependencies if you use `create` method and allows specifying custom factories for creation of deep dependency.

Each fake object is covered by automated tests to make sure that behaviour is correct your tests can test your code specific behaviour by using different scenarios.


## Installation
```bash
composer require --dev ecomdev/magento2-test-essentials
```

## Examples
