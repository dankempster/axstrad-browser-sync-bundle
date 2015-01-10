# AxstradBrowserSyncBundle

__NAMESPACE:__ Axstrad\Bundle\BrowserSyncBundle


## Introduction
For use with [Browser Sync](http://www.browsersync.io/).

When your app is in debug mode (or this is explicitly enabled) the Browser Sync
markup is injected into all HTML responses.

## Installation

Install the bundle using composer.

```
$ composer require --dev axstrad-browser-sync-bundle
```

Add the bundle to your kernel.

```php
// app/AppKernel.php

if ($this->getEnvironment() == 'dev') {
    $bundles[] = new Axstrad\Bundle\BrowPHPerSyncBundle\AxstradBrowserSyncBundle();
}
```
The above assumes you only want browser syncing during development.

That's it! The browser sync markup will be inected into all HTML request so long as the app
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

__server_port__ (Integer): Set what server port to use within the Browser Sync markup.

__client_version__ (String): The installed version of Browser Sync. If specified
it's used as part of the Browser Sync client script URL.
