# Adapt PHP Framework
Adapt is an enterprise grade PHP framework that was designed for rapid web application development and deployment.

Adapt has been used commerically for over 10 years and we are now happy to release 2.0 to the world with an MIT license giving you maximum freedom to do what you do.

## Rapid development
Time is money after all, so with that in mind Adapt has been designed with the following:
* Tiny learning curve, and it's really simple.
* Truly re-usable componets, you will only ever write once with Adapt.
* Many componets already available, from rich fully featured email to enterprise grade permission models.

## Rapid deployment
With so many ways to deploy code, you'd think that we would have let this one go. Nope, we ofter instead, the Adapt way.
* Continuious integration, update production applications with ease and without error.
* Single file installer, deploy to any web server quickly and easily.
* From development, to staging, to production without any complex third party software.

## Adapt 101
Adapt is a PHP framework and a bundle management system all rolled into one. A bundle is just a bunch of useful code packaged with a name and a version number.  Anything written in Adapt must be a bundle, componets are bundles, web applications are bundles, even Adapt is a bundle.

Bundles can depend on bundles, allowing code to be broken down and packged for re-use.  Adapt makes it easy to install bundles, you just add a dependency to your bundle and Adapt downloads and installs it for you.

Adapt offers a lot of bundles so the most common web application features can be included on demand saving many hours of development.

Here is a handful of useful bundles available already:
* Email, full support for sending and receiving, works without any configuration requirements.
* Task scheduling, basically cron for PHP.  Execute large background operations on a regular basis with ease.
* User account management, users and user management made easy.
* Enterprise grade roles and permissions.

## Getting started
### Requirements
* Apache 2.x
* PHP 5.5+
* MySQL/MariaDB (If database support is required)

### Installation
Download [Adapt Installer](https://raw.githubusercontent.com/mbruton/adapt_installer/master/install.php) and upload it to the document root of your web server.  View the file in a browser and the installation will complete automatically, letting you know how to resolve any issues detected during installation.

The installer will download and install Adapt Framework and the Adapt Setup application, Adapt Setup will allow you to configure any database connections you may have and optionaly install an existing application.

### File system layout
Lets say
```
/
```
is the document root, inside you'll find the following:
```
/.htaccess
/index.php
/adapt  <Directory>
```
**.htaccess** Instructs Apache to pass on URL routing to Adapt. **index.php** Handles all requests and initialises the framework. The **adapt** directory contains all the installed bundles, settings and any stored files. 

```
/adapt/store    <Directory>
```

**/adapt/store** is a directory containing any files such as user uploads or any data stored by a bundle during it's life.  Adapt also uses this folder for caching.

```
/adapt/settings.xml
```

**/adapt/settings.xml** the main settings file containing all the settings.

All other directories within **/adapt/** are bundles.

Adapt Setup is within the directory **/adapt/adapt_setup**, within this directory there will be one or more sub directories containing versions of Adapt Setup. For example, **/adapt/adapt_setup/adapt_setup-1.0.0** would contain version 1.0.0 of Adapt Setup.

### Bundle directory layout
Typically bundles will contain the following files and directories:

```
bundle.xml
classes/
controllers/
models/
views/
```

**classes/** contains classes that are not controllers, models or views. The other directories contain what you would expect from their names.

**bundle.xml** contains meta information describing the bundle, such as it's name, version and any dependencies that it has.

## Writing your first application
Create the following directories:
```
/adapt/test_app
/adapt/test_app/test_app-1.0.0
```
We need to tell Adapt about our application so go ahead and create **bundle.xml** and save it in:
```
/adapt/test_app/test_app-1.0.0/bundle.xml
```
Add the following to **bundle.xml**:
```xml
<?xml version="1.0" encoding="utf-8"?>
<adapt_framework>
  <bundle>
    <label>Test Application</label>
    <name>test_app</name>
    <version>1.0.0</version>
    <version_status>release</version_status>
    <type>application</type>
    <namespace>\test_app</namespace>
    <depends_on>
      <bundle>
        <name>adapt</name>
        <version>2.0</version>
      </bundle>
    </depends_on>
  </bundle>
</adapt_framework>
```
**bundle.xml** Tells Adapt that our bundle is an application and that it needs Adapt 2.0 to work.

We now need to create a view controller so that our application can do something useful, create the following directory:
```
/adapt/test_app/test_app-1.0.0/controllers
```
Now create a new PHP file called **controller_root.php** and save it in the controllers directory.

Inside **controller_root.php** add the following:
```php
<?php
namespace test_app;

defined('ADAPT_STARTED') or die;

class controller_root extends \adapt\controller{

  public function view_default(){
    $this->add_view(new html_h1("Hello world"));
    $this->add_view(new html_p("And welcome to Adapt"));
  }

}
```

Before we can view our application in the browser, we must tell Adapt to boot it, to do this simple open **/adapt/settings.xml** and change the value for **adapt.default_application_name** to **test_app** and change the value of **adapt.default_application_version** to **1.0.0**.

Opening the site in a web browers will now display **Hello world**

For more information please visit [AdaptFramework.com](http://www.adaptframework.com)

