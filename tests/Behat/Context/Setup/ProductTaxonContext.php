<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * Overwrites @see \Sylius\Behat\Context\Setup\ProductTaxonContext to fix the issue with assigning already assigned taxon
 */
final readonly class ProductTaxonContext implements Context
{
    public function __construct(
        private FactoryInterface $productTaxonFactory,
        private ObjectManager    $objectManager,
    )
    {
    }

    /**
     * @Given /^I assigned (this product) to ("[^"]+" taxon)$/
     * @Given /^(it|this product) (belongs to "[^"]+")$/
     * @Given /^(this product) is in ("[^"]+" taxon) at (\d)(?:st|nd|rd|th) position$/
     * @Given the product :product belongs to taxon :taxon
     */
    public function itBelongsTo(ProductInterface $product, TaxonInterface $taxon, $position = null)
    {
        if ($product->hasTaxon($taxon)) {
            return;
        }

        $productTaxon = $this->createProductTaxon($taxon, $product, (int)$position - 1);
        $product->addProductTaxon($productTaxon);

        $this->objectManager->persist($product);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(it|this product) (belongs to "[^"]+" and "[^"]+")$/
     */
    public function itBelongsToAnd(ProductInterface $product, iterable $taxons)
    {
        foreach ($taxons as $taxon) {
            if ($product->hasTaxon($taxon)) {
                continue;
            }
            $productTaxon = $this->createProductTaxon($taxon, $product);
            $product->addProductTaxon($productTaxon);
        }

        $this->objectManager->persist($product);
        $this->objectManager->flush();
    }

    /**
     * @Given the product :product has a main taxon :taxon
     * @Given /^(this product) has a main (taxon "[^"]+")$/
     */
    public function productHasMainTaxon(ProductInterface $product, TaxonInterface $taxon): void
    {
        $product->setMainTaxon($taxon);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(it|this product) (belongs to "[^"]+" and "[^"]+" and "[^"]+")$/
     */
    public function itBelongsToAndAnd(
        ProductInterface $product,
        iterable         $taxons,
    ): void
    {
        foreach ($taxons as $taxon) {
            if ($product->hasTaxon($taxon)) {
                continue;
            }
            $productTaxon = $this->createProductTaxon($taxon, $product);
            $product->addProductTaxon($productTaxon);
        }

        $this->objectManager->persist($product);
        $this->objectManager->flush();
    }

    /**
     * @Given the product :product has no main taxon
     * @Given /^(this product) has no main taxon$/
     */
    public function theProductHasNoMainTaxon(ProductInterface $product): void
    {
        $product->setMainTaxon(null);
        $this->objectManager->flush();
    }

    private function createProductTaxon(
        TaxonInterface   $taxon,
        ProductInterface $product,
    ): ProductTaxonInterface
    {
        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setProduct($product);
        $productTaxon->setTaxon($taxon);

        return $productTaxon;
    }
}
