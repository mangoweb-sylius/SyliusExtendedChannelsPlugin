<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Taxon\UpdatePageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Taxon\UpdatePage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['sylius_admin_taxon_update']);

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Channel\UpdatePageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Channel\UpdatePage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args([
            'sylius_admin_channel_update',
            service(\Sylius\Behat\Service\Helper\AutocompleteHelperInterface::class),
        ]);

    $services->set('sylius_extended_channels.context.ui.admin.channel', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingChannelsContext::class)
        ->decorate('sylius.behat.context.ui.admin.managing_channels')
        ->args([
            service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Channel\UpdatePageInterface::class),
            service('sylius.behat.shared_storage'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('sylius_extended_channels.context.ui.admin.product', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingProductContext::class)
        ->args([
            service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product\ShowPageInterface::class),
            service('sylius.behat.notification_checker.admin'),
        ]);

    $services->set('sylius_extended_channels.context.ui.admin.product_variant', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingProductVariantContext::class)
        ->args([
            service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\ProductVariant\ShowPageInterface::class),
            service('sylius.behat.notification_checker.admin'),
        ]);

    $services->set('sylius_extended_channels.context.ui.admin.taxon', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingTaxonContext::class)
        ->args([service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Taxon\UpdatePageInterface::class)]);

    $services->set('sylius_extended_channels.context.domain.email', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Domain\EmailContext::class)
        ->args([service('sylius.behat.email_checker')]);

    $services->set('sylius_extended_channels.context.domain.exchange_rates', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Domain\ExchangeRatesContext::class)
        ->args([service('sylius.repository.exchange_rate')]);

    $services->set('sylius_extended_channels.context.domain.command', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Domain\CommandContext::class)
        ->args([
            service('kernel'),
            service('sylius.console.command.install_sample_data'),
        ]);

    $services->set('sylius_extended_channels.context.ui.shop.product', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Shop\ProductContext::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.repository.channel'),
        ]);

    $services->set('sylius_extended_channels.context.setup.channel', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\ChannelContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.behat.shared_storage'),
            service('sylius.factory.channel'),
        ]);

    $services->set('sylius_extended_channels.context.setup.order', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\OrderContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius.behat.shared_storage'),
            service('sylius.factory.customer'),
            service('sylius.resolver.product_variant.default'),
            service('sylius.factory.order_item'),
            service('sylius.modifier.order_item_quantity'),
            service('sylius.factory.order'),
            service('sylius_abstraction.state_machine'),
            service('kernel'),
        ]);

    $services->set('sylius_extended_channels.context.setup.taxon', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\TaxonContext::class)
        ->args([service('doctrine.orm.entity_manager')]);

    $services->set('sylius_extended_channels.context.ui.shop.order', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Shop\OrderContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('event_dispatcher'),
        ]);

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Order\ShowPageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Order\ShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product\ShowPageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product\ShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\ProductVariant\ShowPageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\ProductVariant\ShowPage::class)
        ->public()
        ->parent('sylius.behat.symfony_page');

    $services->set('sylius.behat.email_checker', \Sylius\Component\Core\Test\Services\EmailChecker::class)
        ->args(['%kernel.cache_dir%/spool']);

    $services->set('mango_sylius.behat.page.admin.product.extended_index', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product\ExtendedIndexPage::class)
        ->public()
        ->parent('sylius.behat.page.admin.product.index')
        ->tag('sylius.behat.page');

    $services->set('mango_sylius.behat.page.admin.bulk_manage_product_categories.form', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\BulkManageProductCategories\FormPage::class)
        ->public()
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
            service('router'),
        ])
        ->tag('sylius.behat.page');

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingBulkProductCategoriesContext::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingBulkProductCategoriesContext::class)
        ->public()
        ->args([
            service('mango_sylius.behat.page.admin.product.extended_index'),
            service('mango_sylius.behat.page.admin.bulk_manage_product_categories.form'),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.repository.product'),
            service('sylius.context.locale'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('fob.context_service');

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\ProductTaxonContext::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\ProductTaxonContext::class)
        ->public()
        ->args([
            service('sylius.factory.product_taxon'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('fob.context_service');

    // Hello Bar Services
    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\IndexPageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\IndexPage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['mangoweb_extended_channels_plugin_admin_hello_bar_index']);

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\CreatePageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\CreatePage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args(['mangoweb_extended_channels_plugin_admin_hello_bar_create']);

    $services->set(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\UpdatePageInterface::class, \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\UpdatePage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.update')
        ->args(['mangoweb_extended_channels_plugin_admin_hello_bar_update']);

    $services->set('sylius_extended_channels.context.setup.hello_bar', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\HelloBarContext::class)
        ->public()
        ->args([
            service('mangoweb_extended_channels_plugin.factory.hello_bar'),
            service('mangoweb_extended_channels_plugin.repository.hello_bar'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('fob.context_service');

    $services->set('sylius_extended_channels.context.ui.admin.managing_hello_bars', \Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingHelloBarsContext::class)
        ->public()
        ->args([
            service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\IndexPageInterface::class),
            service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\CreatePageInterface::class),
            service(\Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\UpdatePageInterface::class),
            service('mangoweb_extended_channels_plugin.repository.hello_bar'),
        ])
        ->tag('fob.context_service');
};
