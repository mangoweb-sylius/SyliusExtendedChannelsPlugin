<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function getRouteName(): string
    {
        return 'mangoweb_extended_channels_plugin_admin_hello_bar_update';
    }

    public function changeTitle(string $title): void
    {
        $this->getDocument()->fillField('Title', $title);
    }

    public function changeMessageType(string $messageType): void
    {
        $this->getDocument()->selectFieldOption('Message type', ucfirst($messageType));
    }

    public function getTitle(): string
    {
        return $this->getElement('title')->getValue();
    }

    public function getMessageType(): string
    {
        return strtolower($this->getElement('messageType')->getValue());
    }

    public function hasChannelAssigned(string $channelName): bool
    {
        return $this->getDocument()->hasCheckedField($channelName);
    }

    public function hasSchedule(string $startDate, string $endDate): bool
    {
        $actualStartDate = $this->getElement('startsAt_date')->getValue();
        $actualStartTime = $this->getElement('startsAt_time')->getValue();
        $actualEndDate = $this->getElement('endsAt_date')->getValue();
        $actualEndTime = $this->getElement('endsAt_time')->getValue();

        return ($actualStartDate . ' ' . $actualStartTime) === $startDate
            && ($actualEndDate . ' ' . $actualEndTime) === $endDate;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'title' => "#hello_bar_translations_en_US_title",
            'content' => '#hello_bar_translations_en_US_content',
            'messageType' => '#hello_bar_messageType',
            'startsAt_date' => '#hello_bar_startsAt_date',
            'startsAt_time' => '#hello_bar_startsAt_time',
            'endsAt_date' => '#hello_bar_endsAt_date',
            'endsAt_time' => '#hello_bar_endsAt_time',
        ]);
    }
}
