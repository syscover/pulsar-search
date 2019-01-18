# Pulsar Search

[![Total Downloads](https://poser.pugx.org/syscover/pulsar-search/downloads)](https://packagist.org/packages/syscover/pulsar-search)

## Installation

**1 - After install Laravel framework, execute on console:**
```
composer require syscover/pulsar-search
```

Register service provider, on file config/app.php add to providers array
```
Syscover\Search\SearchServiceProvider::class,
```

**2 - Execute publish command**
```
php artisan vendor:publish --provider="Syscover\Search\SearchServiceProvider"
```

**3 - To config pulsar search scout driver, set scout configuration in your .env file wit this parameter**
```
SCOUT_DRIVER=pulsar-search
```

**4 - How use this package**

Your models configured with laravel scout will record your data in .json files inside the storage:
```
storage/app/public/search
```

Each file will have the name of the table to which it belongs, from those files you can load this data in the fuse library.js

[fusejs.io](http://fusejs.io)


