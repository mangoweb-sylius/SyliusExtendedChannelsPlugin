<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\BulkManageProductCategories;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\DriverHelper;
use Webmozart\Assert\Assert;

final class FormPage extends SymfonyPage implements FormPageInterface
{
    public function getRouteName(): string
    {
        return 'mangoweb_sylius_admin_bulk_manage_product_categories';
    }

    public function isOpen(array $urlParameters = []): bool
    {
        $currentUrl = $this->getCurrentUrl();
        $expectedUrl = $this->getUrl($urlParameters);
        $expectedPath = parse_url($expectedUrl, PHP_URL_PATH);

        // Check if URL contains the expected path pattern
        return str_contains($currentUrl, $expectedPath);
    }

    public function getCurrentUrl(): string
    {
        return $this->getSession()->getCurrentUrl();
    }

    public function setMainTaxon(string $taxonName): void
    {
        $mainTaxonControl = $this->getElement('main_taxon_control');
        $mainTaxonControl->click();
        $mainTaxonControl->waitFor(
            5,
            fn() => $this->getElement('main_taxon_dropdown')->isVisible()
                && !$this->getElement('main_taxon_dropdown')->hasClass('spinner')
        );

        $this->getElement('main_taxon_dropdown')
            ->waitFor(5, fn() => $this->getElement('main_taxon_dropdown')->hasClass('active'));

        $mainTaxonOption = $this->getElement('main_taxon_dropdown')->find(
            'xpath',
            sprintf('.//div[normalize-space(text())="%s"]', $taxonName)
        );
        Assert::notNull($mainTaxonOption, sprintf('Option with taxon name "%s" not found in the dropdown.', $taxonName));

        $mainTaxonOption->click();
    }

    public function setMainTaxonAction(string $action): void
    {
        $this->getElement('main_taxon_action_select')->selectOption($action);
    }

    public function selectTaxon(string $taxonName): void
    {
        $taxonCheckbox = $this->getElement('taxon_checkbox', ['%taxon_name%' => $taxonName]);
        if (!$taxonCheckbox->isChecked()) {
            $taxonCheckbox->click();
        }
    }

    public function setTaxonsAction(string $action): void
    {
        $this->getElement('taxons_action_select')->selectOption($action);
    }

    public function saveChanges(): void
    {
        $this->getElement('save_button')->press();
        DriverHelper::waitForPageToLoad($this->getSession());
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'main_taxon_field' => '[data-test-bulk-main-taxon-field]',
            'main_taxon_control' => '#bulk_manage_product_categories_mainTaxon-ts-control',
            'main_taxon_dropdown' => '#bulk_manage_product_categories_mainTaxon-ts-dropdown',
            'main_taxon_action_select' => '[data-test-main-taxon-action-select]',
            'taxon_checkbox' => '.infinite-tree-node:contains("%taxon_name%") .form-check-input',
            'taxons_action_select' => '[data-test-taxons-action-select]',
            'save_button' => '[data-test-bulk-save-button]',
        ]);
    }
}
