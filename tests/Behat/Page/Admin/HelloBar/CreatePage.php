<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function getRouteName(): string
    {
        return 'mangoweb_extended_channels_plugin_admin_hello_bar_create';
    }

    public function specifyTitle(string $title): void
    {
        $this->getDocument()->fillField('Title', $title);
    }

    public function specifyContent(string $content): void
    {
        $this->getDocument()->fillField('Content', $content);
    }

    public function specifyMessageType(string $messageType): void
    {
        $this->getDocument()->selectFieldOption('Message type', ucfirst($messageType));
    }

    public function assignToChannel(string $channelName): void
    {
        $this->getDocument()->checkField($channelName);
    }

    public function setStartDate(string $startDate): void
    {
        $dateTime = new \DateTime($startDate);
        $this->getElement('startsAt_date')->setValue($dateTime->format('Y-m-d'));
        $this->getElement('startsAt_time')->setValue($dateTime->format('H:i'));
    }

    public function setEndDate(string $endDate): void
    {
        $dateTime = new \DateTime($endDate);
        $this->getElement('endsAt_date')->setValue($dateTime->format('Y-m-d'));
        $this->getElement('endsAt_time')->setValue($dateTime->format('H:i'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'title' => '#hello_bar_translations_en_US_title',
            'content' => '#hello_bar_translations_en_US_content',
            'messageType' => '#hello_bar_messageType',
            'startsAt_date' => '#hello_bar_startsAt_date',
            'startsAt_time' => '#hello_bar_startsAt_time',
            'endsAt_date' => '#hello_bar_endsAt_date',
            'endsAt_time' => '#hello_bar_endsAt_time',
        ]);
    }
}
