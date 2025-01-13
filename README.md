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

### Testing Class with Store and Configuration

Imagine we have class that depending on current store configuration value view returns different values
Usually you would mock every dependency call in it, which is error-prone as you never with dependency behaviour.

**YourService.php**
```php
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product;

class YourService 
{
    private $storeManager;
    
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly ScopeConfigInterface $scopeConfig
    ) 
    {
    }
    
    public function applyCurrentStoreToProduct(Product $product) 
    {
        $currentStore = $this->storeManager->getStore();
        
        if ($aliasStore = $this->scopeConfig->getValue(
                'catalog/product/store_alias', 
                ScopeInterface::SCOPE_STORE,
                $currentStore->getCode()
            )) {
            $product->setStoreId($this->storeManager->getStore($aliasStore)->getId());
        }
    }
}
```


```php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use EcomDev\Magento2TestEssentials\ScopeConfig;
use EcomDev\Magento2TestEssentials\Store\StoreManager;
use EcomDev\Magento2TestEssentials\ObjectManager;
use Magento\Catalog\Block\Product;

class YourServiceTest extends TestCase
{
    private ObjectManager $objectManager;
    
    protected function setUp() : void
    {
        $this->objectManager =  ObjectManager::new()
            ->withObject(StoreManager::class, StoreManager::new()
                ->withStore(Store::new(3, 'store_three'))
                ->withStore(Store::new(1, 'store_one'))
                ->setCurrentStore('store_three'))
            ->withFactory(ScopeConfig::class, static ($objectManager) => ScopeConfig::new()
                ->withStoreManager($objectManager->get(StoreManager::class))
                ->withStoreValue('store_three', 'catalog/product/store_alias', 1));
    }

    #[Test]
    public function appliesStoreIdToProductWhenFlagIsSet() 
    {
        $service = new YourService(
            $this->objectManager->get(StoreManager::class),
            $this->objectManager->get(ScopeConfig::class)
        );
        
        // Creates product without any constructor
        // but if you rely on data fields, it works great
        $product = $this->objectManager->create(Product::class); 
        
        $applier->applyCurrentStoreToProduct(
            $product
        );
        
        $this->assertEquals(1, $product->getStoreId());
    }
    
     #[Test]
    public function keepsOriginalStoreId() 
    {
        $service = new YourService(
            $this->objectManager->get(StoreManager::class)
                ->setCurrentStore(1),
            $this->objectManager->get(ScopeConfig::class)
        );
        
        $product = $this->objectManager->create(Product::class); 
         
        $applier->applyCurrentStoreToProduct(
            $product
        );
        
        $this->assertEquals(null, $product->getStoreId());
    }
}
```

### Easy Resource Model Testing

```
TBD
```


## Features

- [x] `ObjectManagerInterface` implementation which mimics platform's behaviour
- [x] `StoreInterface` implementation as a simple data object for testing store related behaviour
- [x] `GroupInterface` implementation as a simple data object for testing store group related behaviour
- [x] `WebsiteInterface` implementation as a simple data object for testing website related behaviour
- [x] `ScopeConfigurationInterface` implementation for testing configuration dependent functionality
- [x] `DeploymentConfig` implementation for using in configuration caches, db connections, http cache, etc
- [ ] `ResourceConnection` implementation for quick testing of database components