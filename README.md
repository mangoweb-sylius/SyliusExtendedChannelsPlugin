<p align="center">
    <a href="https://www.mangoweb.cz/en/" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/38423357?s=200&v=4"/>
    </a>
</p>
<h1 align="center">Extended Channels Plugin</h1>

## Features

* Download current exchange rates
* Update product prices using downloaded or custom exchange rates
* Set channel timezone
* Set channel phone
* Send copy of order mail to custom email address per channel
* When SMTP is unavailable, it prevents error 500 on order submit but logs the error and submits the order
* Cancel unpaid orders for certain payment method
* Resend order confirmation email

<p align="center">
	<img src="https://raw.githubusercontent.com/mangoweb-sylius/SyliusExtendedChannelsPlugin/master/doc/admin.png"/>
</p>

<p align="center">
	<img src="https://raw.githubusercontent.com/mangoweb-sylius/SyliusExtendedChannelsPlugin/master/doc/resend.png"/>
</p>

## Installation

1. Run `$ composer require mangoweb-sylius/sylius-extended-channels`.
2. Register `\MangoSylius\ExtendedChannelsPlugin\MangoSyliusExtendedChannelsPlugin` in your Kernel.
3. Your Entity `Channel` has to implement `\MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelInterface`. You can use Trait `MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelTrait`.
4. Include template `Resources/views/Channel/extendedChannelForm.html.twig` in `@SyliusAdmin/Channel/_form.html.twig`.
5. Import `@MangoSyliusExtendedChannelsPlugin/Resources/config/routing.yml` in the routing.yml.
6. Rewrite in `@SyliusAdmin/Order/show.html.twig` row `{{ knp_menu_render(menu, {'template': '@SyliusUi/Menu/top.html.twig'}) }}` by `{{ knp_menu_render(menu, {'template': '@MangoSyliusExtendedChannelsPlugin/Menu/top.html.twig'}) }}`.

For guide to use your own entity see [Sylius docs - Customizing Models](https://docs.sylius.com/en/1.3/customization/model.html)

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
