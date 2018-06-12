# Util URL

[![Build Status](https://travis-ci.org/mrubiosan/util_url.svg?branch=master)](https://travis-ci.org/mrubiosan/util_url)

Provides a single source of truth on how to generate URLs that point to GO1 microservices

## Usage

```php
<?php

use go1\UtilUrl\ServiceUrlGenerator;

//Instance
$serviceUrlGenerator = new ServiceUrlGenerator();
$userServiceUrl = $serviceUrlGenerator->getInternalUrl('user');
$gatewayUrl = $serviceUrlGenerator->getPublicGatewayUrl();

//Static access
$servicesUrls = ServiceUrlGenerator::getInternalUrls(['user', 'lo', 'explore']);
```
