<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function changeTitle(string $title): void;

    public function changeMessageType(string $messageType): void;

    public function getTitle(): string;

    public function getMessageType(): string;

    public function hasChannelAssigned(string $channelName): bool;

    public function hasSchedule(string $startDate, string $endDate): bool;
}
