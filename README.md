stz184's CaptchaBundle
=====================

The `stz184CaptchaBundle` is very simple and ease to use implementation of **captcha** form type for the Symfony2 form component.

The master of this repository is containing current development version, built on top of Symfony v2.5.8.

Installation
============
Installation is a quick 3 step process.

**Step 1:** Add the following to the "require" section of your `composer.json` file:

```
"stz184/captcha-bundle": "dev-master"
```

..and update your project dependencies. 

**Step 2:** Once download, you have to enable the bundle in the kernel (`app/appKernel.php`): 

``` php
<?php
// app/appKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new stz184\CaptchaBundle\stz184CaptchaBundle(),
    );
}
```

**Step 3:** Import stz184CaptchaBundle routing files.
By default, the generated captcha images will be served by /captcha URL

In YAML:

```yml
# app/config/routing.yml
stz184_captcha:
    resource: "@stz184CaptchaBundle/Resources/config/routing.yml"
    prefix:   /
```

Or if you prefer XML:

``` xml
<!-- app/config/routing.xml -->
<import resource="@stz184CaptchaBundle/Resources/config/routing.yml"/>
```

Usage
=====

You can use the **captcha** type in your forms this way:

```php
<?php
    // ...
    $builder->add('captcha', 'captcha'); // That's all !
    // ...
```

License
=======
This bundle is under the MIT license. See the complete license in the bundle:
    LICENSE
