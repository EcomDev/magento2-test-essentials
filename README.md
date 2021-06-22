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

### Testing functionality that tries to resolve a default store

```php
use EcomDev\Magento2TestEssentials\ObjectManager;
use EcomDev\Magento2TestEssentials\Store\StoreManager;
use EcomDev\Magento2TestEssentials\Store\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;

class YourTestedClass 
{
    private $storeManager;
    
    public function __construct(StoreManagerInterface $storeManager) 
    {
        $this->storeManager = $storeManager;
    }
    
    public function applyCurrentStoreToProduct(Product $product) 
    {
        $product->setStoreId($this->storeManager->getStore()->getId());
    }
}

class YourTestedClassTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function appliesStoreIdToProduct() 
    { 
        $applier = new YourTestedClass(
            StoreManager::new()
                ->withStore(Store::new(3, 'store_three'))
                ->setCurrentStore('store_three')
        );
        
        // Creates product without any constructor but if you rely on data fields, it works great
        $product = ObjectManager::new()->get(Product::class); 
        
        $applier->applyCurrentStoreToProduct(
            $product
        );
        
        $this->assertEquals(3, $product->getStoreId());
    }
}
```


## Features

- [x] `ObjectManagerInterface` implementation which mimics platform's behaviour
- [x] `StoreInterface` implementation as a simple data object for testing store related behaviour
- [x] `GroupInterface` implementation as a simple data object for testing store group related behaviour
- [x] `WebsiteInterface` implementation as a simple data object for testing website related behaviour
- [ ] `ScopeConfigurationInterface` implementation for testing configuration dependent functionality
- [ ] `ShoppingCartInterface` implementation for testing shopping cart related functionalities
- [ ] `CustomerInterface` implementation for testing customer related functionalities 
- [ ] `Customer/Session` implementation for testing functionalities that depend on current customer data
- [ ] `Checkout/Session` implementation for testing functionalities that depend on current checkout data
