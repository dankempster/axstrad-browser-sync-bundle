# AxstradBrowserSyncBundle

__NAMESPACE:__ Axstrad\Bundle\BrowserSyncBundle


## Introduction
For use with [Browser Sync](http://www.browsersync.io/).

This bundle will inject the Browser Sync markup into every non-AJAX HTML response sent from
your app. By default, this will occour when your app is in debug mode.

## Installation

Install the bundle using composer.

```
$ composer require --dev axstrad-browser-sync-bundle
```

Add the bundle to your kernel.

```php
// app/AppKernel.php

if ($this->getEnvironment() == 'dev') {
    $bundles[] = new Axstrad\Bundle\BrowserSyncBundle\AxstradBrowserSyncBundle();
}
```

That's it! The browser sync markup will be inected into all HTML requests so long as your app
is in debug mode.

## Configuration Reference

Full config with default values:

```yaml
axstrad_browser_sync:
    enabled: %kernel.debug%
    server_port: 3000
    client_version: null
```

__enabled__ (Boolean): Enable (true) or disable (false) the bundle. If null
the value of the kernel's debug parameter is inherited.

__server_port__ (Integer): Set the server port to use within the Browser Sync markup.

__client_version__ (String): Your installed version of Browser Sync, you may leave this blank.

