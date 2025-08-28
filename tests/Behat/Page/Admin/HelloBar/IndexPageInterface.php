<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function hasVariousMessageTypes(): bool;

    public function updateResourceByName(string $name): void;

    public function deleteResourceByName(string $name): void;

    public function showResourceByName(string $name): void;
}