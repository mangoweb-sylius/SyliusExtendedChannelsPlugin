<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\DriverHelper;
use Webmozart\Assert\Assert;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'mangoweb_extended_channels_plugin_admin_hello_bar_index';
    }

    public function hasVariousMessageTypes(): bool
    {
        $messageTypes = [];
        $rows = $this->getDocument()->findAll('css', 'tbody tr');

        foreach ($rows as $row) {
            $messageTypeCell = $row->find('css', 'td:nth-child(2)');
            Assert::notNull($messageTypeCell, 'Message type cell should be present in the table row.');
            $messageTypes[] = $messageTypeCell->getText();
        }

        return count(array_unique($messageTypes)) > 1;
    }

    public function updateResourceByName(string $name): void
    {
        $actions = $this->getActionsForResource(['title' => $name]);
        $editLink = $actions->find('css', '[data-test-action="update"]');

        if ($editLink === null) {
            $editLink = $actions->find('css', 'a[href*="edit"]');
        }

        Assert::notNull($editLink, 'Edit link should be present on the index page.');

        $editLink->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function deleteResourceByName(string $name): void
    {
        $this->deleteResourceOnPage(['title' => $name]);
    }

    public function showResourceByName(string $name): void
    {
        // link to show is only on the edit page
        $this->updateResourceByName($name);

        $showLink = $this->getDocument()->find('css', '[data-test-show-hello_bar]');
        Assert::notNull($showLink, 'Show link should be present on the edit page.');

        $showLink->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }
}
