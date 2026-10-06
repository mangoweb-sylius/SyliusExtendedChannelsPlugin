<?php

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/mark_taxon_as_external_link.php',
        'suites/managing_channel.php',
        'suites/send_order_email_to_bcc_email.php',
        'suites/cancel_unpaid_orders_for_certain_payment_method.php',
        'suites/update_product_prices_using_exchange_rates.php',
        'suites/download_current_exchange_rates.php',
        'suites/duplicate_product.php',
        'suites/duplicate_product_variant.php',
        'suites/bulk_manage_product_categories.php',
        'suites/managing_hello_bars.php',
    ]);
