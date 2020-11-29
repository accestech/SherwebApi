# PHP Implementation for Sherweb API
This is a crude implantation for the Sherweb API

In the current state, this is more a proof of concept than a library

# Example

*DO NOT USE IN PRODUCTION YET!*

```shell script
# The package does not exists yet ...
composer require accestech/sherweb-api
```

```php
<?php
require ('vendor/autoload.php');

use Accestech\SherwebApi\SherwebApi;

// The only scope currently supported by the API is 'distributor' 
$api = new SherwebApi('api_client_id', 'api_client_secret', 'scope');

$api->distributor()->getPayableCharges();
```

# Todo
 - [ ] Error handling
 - [ ] Unit testing