<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup\ProductTaxonContext;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin\ManagingBulkProductCategoriesContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withSuite(
                (new Suite('bulk_manage_product_categories'))
                    ->withContexts(
                        'sylius.behat.context.hook.doctrine_orm',
                        'sylius.behat.context.transform.channel',
                        'sylius.behat.context.transform.currency',
                        'sylius.behat.context.transform.customer',
                        'sylius.behat.context.transform.locale',
                        'sylius.behat.context.transform.product',
                        'sylius.behat.context.transform.shared_storage',
                        'sylius.behat.context.transform.taxon',
                        'sylius.behat.context.transform.user',
                        'sylius.behat.context.setup.channel',
                        'sylius.behat.context.setup.currency',
                        'sylius.behat.context.setup.locale',
                        'sylius.behat.context.setup.admin_security',
                        'sylius.behat.context.setup.product',
                        'sylius.behat.context.setup.taxonomy',
                        'sylius.behat.context.setup.user',
                        'sylius.behat.context.ui.admin.notification',
                        ProductTaxonContext::class,
                        ManagingBulkProductCategoriesContext::class,
                    )
                    ->withFilter(new TagFilter('@bulk_manage_product_categories')),
            ),
    );
