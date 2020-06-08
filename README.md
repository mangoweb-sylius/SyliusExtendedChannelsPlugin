<p align="center">
    <a href="https://www.mangoweb.cz/en/" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/38423357?s=200&v=4"/>
    </a>
</p>
<h1 align="center">
Extended Channels Plugin
<br />
    <a href="https://packagist.org/packages/mangoweb-sylius/sylius-extended-channels" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/mangoweb-sylius/sylius-extended-channels.svg" />
    </a>
    <a href="https://packagist.org/packages/mangoweb-sylius/sylius-extended-channels" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/mangoweb-sylius/sylius-extended-channels.svg" />
    </a>
    <a href="http://travis-ci.org/mangoweb-sylius/SyliusExtendedChannelsPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/mangoweb-sylius/SyliusExtendedChannelsPlugin/master.svg" />
    </a>
    <br />
    <img src="https://sylius.com/assets/badge-approved-by-sylius.png" alt="Approved by Sylius" width="120"/>
</h1>

## Features

* Duplicate product and product variant
* Bulk action to set categories for products
* Mark Taxon as external link so taxonomy can be used for creating custom menus with links anywhere
* Download current exchange rates
* Update product prices using downloaded or custom exchange rates
* Set channel timezone
* Set channel phone
* Send copy of order mail to custom email address per channel
* When SMTP is unavailable, it prevents error 500 on order submit but logs the error and submits the order
* Cancel unpaid orders for certain payment method
* Allows to change the code for the product and product variant
* Administration for Hello Bars (you can use your own types)

<p align="center">
	<img src="https://raw.githubusercontent.com/mangoweb-sylius/SyliusExtendedChannelsPlugin/master/doc/admin.png"/>
</p>

## Installation

1. Run `$ composer require mangoweb-sylius/sylius-extended-channels`.
1. Add plugin classes to your `config/bundles.php`:
 
   ```php
   return [
      ...
      MangoSylius\ExtendedChannelsPlugin\MangoSyliusExtendedChannelsPlugin::class => ['all' => true],
   ];
   ```
   
1. Your Entity `Channel` has to implement `\MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelInterface`. You can use Trait `MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelTrait`.
1. Your Entity `Taxon` has to implement `\MangoSylius\ExtendedChannelsPlugin\Model\ExternalLinkTaxonInterface`. You can use Trait `MangoSylius\ExtendedChannelsPlugin\Model\ExternalLinkTaxonTrait`.
1. Include template `{{ include('@MangoSyliusExtendedChannelsPlugin/Channel/extendedChannelForm.html.twig') }}` in `@SyliusAdmin/Channel/_form.html.twig`.
1. Add `{{ form_row(form.externalLink) }}` to template in `@SyliusAdmin/Taxon/_form.html.twig`.
1. Replace inner content of `<div class="sylius-grid-nav__bulk"> ... </div>` with `{{ include('@MangoSyliusExtendedChannelsPlugin/Grid/bulkActions.html.twig') }}` in `@SyliusAdmin/Grid/_default.html.twig`.
1. Add resource to `config/packeges/_sylius.yaml`

    ```yaml
    imports:
         ...
         - { resource: "@MangoSyliusExtendedChannelsPlugin/Resources/config/resources.yml" }
    ```
   
1. Add routing to `config/_routes.yaml`

    ```yaml
    mango_sylius_extended_channels:
        resource: '@MangoSyliusExtendedChannelsPlugin/Resources/config/routing.yml'
        prefix: /admin
    ```


For guide to use your own entity see [Sylius docs - Customizing Models](https://docs.sylius.com/en/1.7/customization/model.html)

### Optional

Run `src/Migrations/basic-data/timezones-data.sql` for load the timezones table. (Recommended, otherwise the timezone select will be empty)

## Usage

### Commands
* Updates exchange rates (you need to define currencies first in Sylius admin)

  ```bash
  mango:exchange-rates:update
  ```


* Update Product prices by exchange rates, from `sourceChannel` (primary value, won't be changed) to `targetChannel`. You can run this after the previous command to update by downloaded rates or you can run it without the previous one to update the prices with your custom exchange rates set in Sylius admin.

   ```bash
   mango:product:update-price sourceChannel targetChannel
   ```


* Remove orders that are not paid for a configured period and for certain shipping methods. This allows to keep unpaid orders which are e.g. to be paid at personal pickup, therefore needs to stay unpaid for a couple of hours / days. Configuration parameters:
    * `sylius_order.order_expiration_period`
    * `sylius_order.expiration_method_codes`
    

   ```bash
   mango:cancel-unpaid-orders
   ```

* You can use events to modify an object when you duplicate it
    * `mango-sylius-extended-channels.duplicate.product.before-persist`
    * `mango-sylius-extended-channels.duplicate.product.after-persist`
    * `mango-sylius-extended-channels.duplicate.product-variant.before-persist`
    * `mango-sylius-extended-channels.duplicate.product-variant.after-persist`

* You can change the types of Hello bars
    ```yaml
    parameters:
        mangoweb_sylius_extended_channels_hello_bar_types:
            error: 'Error'
            success: 'Success'
            info: 'Info'
            warning: 'Warning'
    ```
  
* Use the Twig function for listing Hello Bars 
    * `mangoweb_sylius_available_hello_bars()`
    * `mangoweb_sylius_available_hello_bars_by_type(type)`

## Development

### Usage

- Create symlink from .env.dist to .env or create your own .env file
- Develop your plugin in `/src`
- See `bin/` for useful commands

### Testing

After your changes you must ensure that the tests are still passing.

```bash
$ composer install
$ bin/console doctrine:schema:create -e test
$ bin/behat
$ bin/phpstan.sh
$ bin/ecs.sh
```

License
-------
This library is under the MIT license.

Credits
-------
Developed by [manGoweb](https://www.mangoweb.eu/).
