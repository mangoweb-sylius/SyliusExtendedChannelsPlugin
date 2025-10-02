<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyTitle(string $title): void;

    public function specifyContent(string $content): void;

    public function specifyMessageType(string $messageType): void;

    public function assignToChannel(string $channelName): void;

    public function setStartDate(string $startDate): void;

    public function setEndDate(string $endDate): void;
}