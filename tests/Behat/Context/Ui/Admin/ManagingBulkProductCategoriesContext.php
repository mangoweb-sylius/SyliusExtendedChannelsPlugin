<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product\ExtendedIndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\BulkManageProductCategories\FormPageInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingBulkProductCategoriesContext implements Context
{
    public function __construct(
        private ExtendedIndexPageInterface   $productIndexPage,
        private FormPageInterface            $bulkManageCategoriesPage,
        private NotificationCheckerInterface $notificationChecker,
        private ProductRepositoryInterface   $productRepository,
        private LocaleContextInterface       $localeContext,
        private TranslatorInterface          $translator,
        private EntityManagerInterface       $entityManager,
    )
    {
    }

    /**
     * @When I browse products
     */
    public function iBrowseProducts(): void
    {
        $this->productIndexPage->open();
    }

    /**
     * @When I select the :productName1 and :productName2 products for bulk action
     */
    public function iSelectTheProductsForBulkAction(
        string $productName1,
        string $productName2,
    ): void
    {
        $this->productIndexPage->selectBulkAction($productName1);
        $this->productIndexPage->selectBulkAction($productName2);
    }

    /**
     * @When I select the :productName1, :productName2 and :productName3 products for bulk action
     */
    public function iSelectTheThreeProductsForBulkAction(
        string $productName1,
        string $productName2,
        string $productName3,
    ): void
    {
        $this->productIndexPage->selectBulkAction($productName1);
        $this->productIndexPage->selectBulkAction($productName2);
        $this->productIndexPage->selectBulkAction($productName3);
    }

    /**
     * @When I choose bulk action :actionName
     */
    public function iChooseBulkAction(string $actionName): void
    {
        $this->productIndexPage->chooseBulkAction($actionName);
    }

    /**
     * @Then I should be on the bulk manage product categories page with selected products :productName1 and :productName2
     * @Then I should be on the bulk manage product categories page with selected products :productName1, :productName2 and :productName3
     */
    public function iShouldBeOnTheBulkManageProductCategoriesPage(
        string  $productName1,
        string  $productName2,
        ?string $productName3 = null,
    ): void
    {
        $product1 = $this->getProductByName($productName1);
        $product2 = $this->getProductByName($productName2);
        $product3 = $productName3 !== null ? $this->getProductByName($productName3) : null;
        $productIds = [$product1->getId(), $product2->getId()];
        if ($product3 !== null) {
            $productIds[] = $product3->getId();
        }
        Assert::true(
            $this->bulkManageCategoriesPage->isOpen(['bulkProductsIds' => $productIds])
        );
    }

    /**
     * @When I set main taxon to :taxonName with :action action
     */
    public function iSetMainTaxonToWithAction(
        string $taxonName,
        string $action,
    ): void
    {
        $this->bulkManageCategoriesPage->setMainTaxon($taxonName);
        $this->bulkManageCategoriesPage->setMainTaxonAction($action);
    }

    /**
     * @When I set main taxon with :action action
     */
    public function iSetMainTaxonWithAction(string $action): void
    {
        $this->bulkManageCategoriesPage->setMainTaxonAction($action);
    }

    /**
     * @When I set taxons to :taxonName1 and :taxonName2 with :action action
     */
    public function iSetTaxonsToWithAction(
        string $taxonName1,
        string $taxonName2,
        string $action,
    ): void
    {
        $this->bulkManageCategoriesPage->selectTaxon($taxonName1);
        $this->bulkManageCategoriesPage->selectTaxon($taxonName2);
        $this->bulkManageCategoriesPage->setTaxonsAction($action);
    }

    /**
     * @When I set taxons to :taxonName with :action action
     */
    public function iSetTaxonsToSingleWithAction(
        string $taxonName,
        string $action,
    ): void
    {
        $this->bulkManageCategoriesPage->selectTaxon($taxonName);
        $this->bulkManageCategoriesPage->setTaxonsAction($action);
    }

    /**
     * @When I set taxons with :action action
     */
    public function iSetTaxonsWithAction(string $action): void
    {
        $this->bulkManageCategoriesPage->setTaxonsAction($action);
    }

    /**
     * @When I save the bulk categories changes
     */
    public function iSaveTheBulkCategoriesChanges(): void
    {
        $this->bulkManageCategoriesPage->saveChanges();
        $this->entityManager->clear();
    }

    /**
     * @Then I should be notified that the categories have been successfully saved
     */
    public function iShouldBeNotifiedThatTheCategoriesHaveBeenSuccessfullySaved(): void
    {
        for ($attempts = 0; $attempts < 5; $attempts++) {
            try {
                $this->notificationChecker->checkNotification(
                    $this->translator->trans('mango-sylius.admin.manage_product_categories.saved'),
                    NotificationType::success(),
                );
                return;
            } catch (StaleElementReferenceException $staleElementReferenceException) {
                // Wait a bit for the notification to appear in DOM to prevent StaleElementReferenceException
                usleep(100000); // 100ms
            }
        }
        throw $staleElementReferenceException;
    }

    /**
     * @Then I should be redirected to the product index page
     */
    public function iShouldBeRedirectedToTheProductIndexPage(): void
    {
        $this->productIndexPage->verify();
    }

    /**
     * @Then the :productName product should have :taxonName as its main taxon
     */
    public function theProductShouldHaveAsItsMainTaxon(
        string $productName,
        string $taxonName,
    ): void
    {
        $product = $this->getProductByName($productName);
        $mainTaxon = $product->getMainTaxon();

        Assert::notNull($mainTaxon, sprintf('Product "%s" should have a main taxon', $productName));
        Assert::eq($mainTaxon->getName(), $taxonName, sprintf('Product "%s" main taxon should be "%s", but is "%s"', $productName, $taxonName, $mainTaxon->getName()));
    }

    /**
     * @Then the :productName product should have no main taxon
     */
    public function theProductShouldHaveNoMainTaxon(string $productName): void
    {
        $product = $this->getProductByName($productName);
        Assert::null($product->getMainTaxon(), sprintf('Product "%s" should not have a main taxon', $productName));
    }

    /**
     * @Then the :productName product should belong to :taxonName1 and :taxonName2 taxons
     */
    public function theProductShouldBelongToTaxons(
        string $productName,
        string $taxonName1,
        string $taxonName2,
    ): void
    {
        $product = $this->getProductByName($productName);
        $productTaxons = $product->getTaxons();

        $currentTaxonNames = [];
        foreach ($productTaxons as $taxon) {
            $currentTaxonNames[] = $taxon->getName();
        }

        $expectedTaxonNames = [$taxonName1, $taxonName2];
        sort($currentTaxonNames);
        sort($expectedTaxonNames);
        $missingTaxons = array_diff($expectedTaxonNames, $currentTaxonNames);
        $unexpectedTaxons = array_diff($currentTaxonNames, $expectedTaxonNames);
        Assert::true(
            empty($missingTaxons) && empty($unexpectedTaxons),
            sprintf(
                'Product "%s" taxons do not match expected. Missing: [%s]. Unexpected: [%s]',
                $productName,
                implode(', ', $missingTaxons),
                implode(', ', $unexpectedTaxons),
            ),
        );
    }

    /**
     * @Then the :productName product should belong to :taxonName1, :taxonName2 and :taxonName3 taxons
     */
    public function theProductShouldBelongToThreeTaxons(
        string $productName,
        string $taxonName1,
        string $taxonName2,
        string $taxonName3,
    ): void
    {
        $product = $this->getProductByName($productName);
        $productTaxons = $product->getTaxons();

        $taxonNames = [];
        foreach ($productTaxons as $taxon) {
            $taxonNames[] = $taxon->getName();
        }

        Assert::inArray($taxonName1, $taxonNames, sprintf('Product "%s" should belong to taxon "%s"', $productName, $taxonName1));
        Assert::inArray($taxonName2, $taxonNames, sprintf('Product "%s" should belong to taxon "%s"', $productName, $taxonName2));
        Assert::inArray($taxonName3, $taxonNames, sprintf('Product "%s" should belong to taxon "%s"', $productName, $taxonName3));
        Assert::count($taxonNames, 3, sprintf('Product "%s" should belong to exactly 3 taxons, but belongs to %d', $productName, count($taxonNames)));
    }

    /**
     * @Then the :productName product should belong to :taxonName taxon only
     */
    public function theProductShouldBelongToTaxonOnly(
        string $productName,
        string $taxonName,
    ): void
    {
        $product = $this->getProductByName($productName);
        $productTaxons = $product->getTaxons();

        Assert::count($productTaxons, 1, sprintf('Product "%s" should belong to exactly 1 taxon, but belongs to %d', $productName, count($productTaxons)));

        $taxon = $productTaxons->first();
        Assert::notFalse($taxon, sprintf('Product "%s" should have at least one taxon', $productName));
        Assert::eq($taxon->getName(), $taxonName, sprintf('Product "%s" should belong only to taxon "%s", but belongs to "%s"', $productName, $taxonName, $taxon->getName()));
    }

    /**
     * @Then the :productName product should have no taxons
     */
    public function theProductShouldHaveNoTaxons(string $productName): void
    {
        $product = $this->getProductByName($productName);
        Assert::count($product->getTaxons(), 0, sprintf('Product "%s" should not belong to any taxon', $productName));
    }


    private function getProductByName(string $productName): ProductInterface
    {
        $products = $this->productRepository->findByName(
            $productName,
            $this->localeContext->getLocaleCode(),
        );
        Assert::greaterThan(count($products), 0, sprintf('Product "%s" not found', $productName));
        Assert::count($products, 1, sprintf('Multiple products with name "%s" found', $productName));

        return reset($products);
    }
}
