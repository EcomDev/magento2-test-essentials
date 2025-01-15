# 🎯 Essentials for testing Magento 2 modules
[![PHP Package](https://github.com/EcomDev/magento2-test-essentials/actions/workflows/php-package.yml/badge.svg)](https://github.com/EcomDev/magento2-test-essentials/actions/workflows/php-package.yml)

Using mocking frameworks for testing Magento 2 modules is counterproductive as you replicate line by line your actual calls to a magento implementation.

Binding your tests to the details of implementation leads to very fragile test suite that no-one wants to work with afterwards as a small change in underlying code like extraction of the class requires complete rewrite of the test case. 

This package solves this problem by providing `fake objects` for most common operations you might want to interact with core.

As well as set of fake objects there is an  `ObjectManagerInterface` implementation that automatically instantiates dependencies if you use `create` method and allows specifying custom factories for creation of deep dependency.

Each fake object is covered by automated tests to make sure that behaviour is correct your tests can test your code specific behaviour by using different scenarios.


## 📦 Installation
```bash
composer require --dev ecomdev/magento2-test-essentials
```

For database based tests it is recommended also to install collection of [testcontainers](https://github.com/EcomDev/testcontainer-magento-data):
```bash
composer require --dev ecomdev/testcontainers-magento-data
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

**YourServiceTest.php**
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
        $product = $this->objectManager->get(Product::class); 
        
        $service->applyCurrentStoreToProduct(
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
        
        $product = $this->objectManager->get(Product::class);
         
        $service->applyCurrentStoreToProduct(
            $product
        );
        
        $this->assertEquals(null, $product->getStoreId());
    }
}
```

### Easy Real Database Integration Testing

In combination with magento data [testcontainers](https://github.com/EcomDev/testcontainer-magento-data-php) it is possible to write quick integration tests

Imagine your service depends on Magento's `ResourceConnection` which is almost impossible to instantiate without installing whole Magento app:
```php
use Magento\Framework\App\ResourceConnection;

class SomeSimpleService 
{
    public function __construct(private readonly ResourceConnection $resourceConnection)
    {
    }
    
    public function totalNumberOfSimpleProducts(): int 
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $this->resourceConnection->getTableName('catalog_product_entity'),
                 ['count' => 'COUNT(*)']
             )
            ->where('type_id = ?', 'simple')
            
        return (int)$connection->fetchOne($select);
    }    
}
```

Now you can create all the required dependencies with the help of `IntegrationUtility` by specifying connection details:
```php

use EcomDev\TestContainers\MagentoData\DbConnectionSettings;
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;
use Magento\Framework\App\ResourceConnection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IntegrationUtilityTest extends TestCase
{
    #[Test]
    public function returnsCorrectAmountOfSimpleProductsInSampleDataDb()
    {
        $connection = DbContainerBuilder::mysql()
            ->withSampleData()
            ->build();
        
        $connectionSettings = $connection->getConnectionSettings();
        
        $objectManager = IntegrationUtility::setupDatabaseObjects(
            DeploymentConfig::new()
                ->withDatabaseConnection(
                    $connectionSettings->host,
                    $connectionSettings->user,
                    $connectionSettings->password,
                    $connectionSettings->database
                )
        );
        
        $service = $objectManager->get(SomeSimpleService::class);
        
        $this->assertEquals(
            1891,
            $service->totalNumberOfSimpleProducts()
        );
    }
}
```

Now there is no excuse to not write tests for your database components!

##  ✨ Features

- [x] `ObjectManagerInterface` implementation which mimics platform's behaviour
- [x] `StoreInterface` implementation as a simple data object for testing store related behaviour
- [x] `GroupInterface` implementation as a simple data object for testing store group related behaviour
- [x] `WebsiteInterface` implementation as a simple data object for testing website related behaviour
- [x] `ScopeConfigurationInterface` implementation for testing configuration dependent functionality
- [x] `DeploymentConfig` implementation for using in configuration caches, db connections, http cache, etc
- [x] `ResourceConnection` implementation for quick testing of database components

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
