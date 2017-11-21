ACFWidgets Plugin 
===================

This is a project worked on to improve quality of code around making WordPress plugins. It aims to solve a few issues that other ACFWidgetss fail to address. These include

- Namespacing & Autoloading
- Generic ACF & CPT examples and loading
- No convoluted routing or file includes 
- Simple structure
- Htaccess to block access to our files 
- Composer to force PHP versions

This is maintained by 4mation - Harlan Wilton <harlan.wilton@4mation.com.au>

Min Requirements
---------

- PHP 7
- WordPress 4.9.0

Setup
-------------

#### **Environment**

The outcome is to exchange security for ease of use, for that reason it's important that you take the security measures needed 
to ensure that someone can't take advantage of the site with this plugin enabled.

If you're running on a staging environment ensure you have setup a [htpasswd](http://www.htaccesstools.com/htpasswd-generator/) 

#### **Instructions**

To use the plugin, it must be able to detect the environment is development or staging, this can be done:

Via **wp-config.php**
1. Add `define('WP_ENV', 'development')`

Via **vhost**
1. Add `SetEnv WP_ENV "development"`


#### **Filters**

```
development-environment/is-development
```
Set yourself how the plugin detects if the environment is development. 

```
development-environment/require-component-$component
```
Disable require of a component if you don't want to use it. Possible values are: 
`disallow-indexing`, `flush-rewrites`, `no-password-logins`, `whoops-error-handling` 
