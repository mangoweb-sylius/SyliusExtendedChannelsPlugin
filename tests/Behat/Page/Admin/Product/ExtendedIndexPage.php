<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\IndexPage;
use Webmozart\Assert\Assert;

final class ExtendedIndexPage extends IndexPage implements ExtendedIndexPageInterface
{
    public function selectBulkAction(string $productName): void
    {
        // Use the exact same pattern as Sylius checkResourceOnPage method
        $tableAccessor = $this->getTableAccessor();
        $table         = $this->getElement('table');
        $resourceRow   = $tableAccessor->getRowWithFields($table, ['name' => $productName]);
        $bulkCheckbox  = $resourceRow->find('css', '.form-check-input');
        Assert::notNull($bulkCheckbox);
        $bulkCheckbox->click();
    }

    public function chooseBulkAction(string $actionName): void
    {
        $locator = '#bulk-' . preg_replace('~^bulk-~', '', $actionName);
        $session = $this->getSession();
        $page    = $session->getPage();
        $form    = $page->find('css', $locator);
        Assert::notNull($form, "Form not found by locator '{$locator}'");

        $submitButton = $form->find('css', 'button[type="submit"]');
        Assert::notNull($submitButton, 'Submit button not found');
        $submitButton->click();
    }
}
