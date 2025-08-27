<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\IndexPageInterface;

interface ExtendedIndexPageInterface extends IndexPageInterface
{
    public function selectBulkAction(string $productName): void;

    public function chooseBulkAction(string $actionName): void;
}
