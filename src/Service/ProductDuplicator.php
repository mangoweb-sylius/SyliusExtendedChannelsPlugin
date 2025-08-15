<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Service;

use Doctrine\ORM\EntityRepository;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductDuplicator implements ProductDuplicatorInterface
{
    public function __construct(private readonly ImageUploaderInterface $imageUploader, private readonly ProductVariantRepositoryInterface $productVariantRepository, private readonly ProductRepositoryInterface $productRepository, private readonly DataManager $dataManager)
    {
    }

    public function duplicateProduct(ProductInterface $oldEntity): ProductInterface
    {
        $class = $oldEntity::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductInterface);

        assert($oldEntity->getCode() !== null);

        $newEntity->setCode($this->generateProductCode($oldEntity->getCode()));

        $newEntity->setEnabled(false);
        $newEntity->setVariantSelectionMethod($oldEntity->getVariantSelectionMethod());

        $newEntity->setMainTaxon($oldEntity->getMainTaxon());

        $this->duplicateProductAssociations($newEntity, $oldEntity);
        $this->duplicateProductTranslations($newEntity, $oldEntity);
        $this->duplicateProductAttributes($newEntity, $oldEntity);
        $this->duplicateProductChannels($newEntity, $oldEntity);
        $this->duplicateProductTaxons($newEntity, $oldEntity);
        $this->duplicateProductImages($newEntity, $oldEntity);

        $this->duplicateProductVariants($newEntity, $oldEntity);

        return $newEntity;
    }

    public function duplicateProductVariant(
        ProductInterface $product,
        ProductVariantInterface $oldEntity,
    ): ProductVariantInterface {
        $class = $oldEntity::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductVariantInterface);

        $newEntity->setProduct($product);

        assert($oldEntity->getCode() !== null);
        $newEntity->setCode($this->generateProductVariantCode($oldEntity->getCode()));

        $newEntity->setTracked($oldEntity->isTracked());
        $newEntity->setDepth($oldEntity->getDepth());
        $newEntity->setHeight($oldEntity->getHeight());
        $newEntity->setWidth($oldEntity->getWidth());
        $newEntity->setWeight($oldEntity->getWeight());
        $newEntity->setShippingRequired($oldEntity->isShippingRequired());

        $newEntity->setShippingCategory($oldEntity->getShippingCategory());
        $newEntity->setTaxCategory($oldEntity->getTaxCategory());

        $this->duplicateProductVariantTranslations($newEntity, $oldEntity);
        $this->duplicateOptionValues($newEntity, $oldEntity);
        $this->duplicateChannelPricings($newEntity, $oldEntity);

        return $newEntity;
    }

    public function duplicateProductImages(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getImages() as $image) {
            assert($image instanceof ProductImageInterface);
            $newEntity->addImage($this->duplicateProductImage($newEntity, $image));
        }
    }

    public function duplicateProductAssociations(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getAssociations() as $association) {
            $newEntity->addAssociation($this->duplicateProductAssociation($newEntity, $association));
        }
    }

    public function duplicateProductAttributes(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getAttributes() as $attribute) {
            $newEntity->addAttribute($this->duplicateProductAttribute($attribute));
        }
    }

    public function duplicateProductTaxons(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getProductTaxons() as $productTaxon) {
            $newEntity->addProductTaxon($this->duplicateProductTaxon($newEntity, $productTaxon));
        }
    }

    public function duplicateProductTranslations(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getTranslations() as $translation) {
            assert($translation instanceof ProductTranslationInterface);
            $newEntity->addTranslation($this->duplicateProductTranslation($newEntity, $translation));
        }
    }

    public function duplicateProductVariants(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getVariants() as $variant) {
            assert($variant instanceof ProductVariantInterface);
            $newEntity->addVariant($this->duplicateProductVariant($newEntity, $variant));
        }
    }

    public function duplicateProductVariantTranslations(
        ProductVariantInterface $newEntity,
        ProductVariantInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getTranslations() as $translation) {
            assert($translation instanceof ProductVariantTranslationInterface);
            $newEntity->addTranslation($this->duplicateProductVariantTranslation($newEntity, $translation));
        }
    }

    public function duplicateProductChannels(
        ProductInterface $newEntity,
        ProductInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getChannels() as $channel) {
            $newEntity->addChannel($channel);
        }
    }

    public function duplicateOptionValues(
        ProductVariantInterface $newEntity,
        ProductVariantInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getOptionValues() as $optionValue) {
            $newEntity->addOptionValue($optionValue);
        }
    }

    public function duplicateChannelPricings(
        ProductVariantInterface $newEntity,
        ProductVariantInterface $oldEntity,
    ): void {
        foreach ($oldEntity->getChannelPricings() as $pricing) {
            $newEntity->addChannelPricing($this->duplicateChannelPricing($newEntity, $pricing));
        }
    }

    public function duplicateChannelPricing(
        ProductVariantInterface $productVariant,
        ChannelPricingInterface $pricing,
    ): ChannelPricingInterface {
        $class = $pricing::class;
        $newEntity = new $class();
        assert($newEntity instanceof ChannelPricingInterface);

        $newEntity->setOriginalPrice($pricing->getOriginalPrice());
        $newEntity->setPrice($pricing->getPrice());
        $newEntity->setChannelCode($pricing->getChannelCode());
        $newEntity->setProductVariant($productVariant);

        return $newEntity;
    }

    public function duplicateProductVariantTranslation(
        ProductVariantInterface $productVariant,
        ProductVariantTranslationInterface $translation,
    ): ProductVariantTranslationInterface {
        $class = $translation::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductVariantTranslationInterface);

        $newEntity->setLocale($translation->getLocale());
        $newEntity->setName($translation->getName());
        $newEntity->setTranslatable($productVariant);

        return $newEntity;
    }

    public function duplicateProductTaxon(
        ProductInterface $product,
        ProductTaxonInterface $productTaxon,
    ): ProductTaxonInterface {
        $class = $productTaxon::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductTaxonInterface);

        $newEntity->setProduct($product);
        $newEntity->setTaxon($productTaxon->getTaxon());

        return $newEntity;
    }

    public function duplicateProductAttribute(AttributeValueInterface $attributeValue): AttributeValueInterface
    {
        $class = $attributeValue::class;
        $newEntity = new $class();
        assert($newEntity instanceof AttributeValueInterface);

        $newEntity->setAttribute($attributeValue->getAttribute());
        $newEntity->setValue($attributeValue->getValue());
        $newEntity->setLocaleCode($attributeValue->getLocaleCode());

        return $newEntity;
    }

    public function duplicateProductAssociation(
        ProductInterface $product,
        ProductAssociationInterface $association,
    ): ProductAssociationInterface {
        $class = $association::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductAssociationInterface);

        $newEntity->addAssociatedProduct($product);
        $newEntity->setOwner($association->getOwner());
        $newEntity->setType($association->getType());

        return $newEntity;
    }

    public function duplicateProductTranslation(
        ProductInterface $product,
        ProductTranslationInterface $translation,
    ): ProductTranslationInterface {
        $class = $translation::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductTranslationInterface);

        assert($translation->getSlug() !== null);
        assert($translation->getLocale() !== null);

        $newEntity->setSlug($this->generateSlug($translation->getSlug(), $translation->getLocale()));

        $newEntity->setTranslatable($product);
        $newEntity->setName($translation->getName());
        $newEntity->setLocale($translation->getLocale());
        $newEntity->setShortDescription($translation->getShortDescription());
        $newEntity->setDescription($translation->getDescription());
        $newEntity->setMetaDescription($translation->getMetaDescription());
        $newEntity->setMetaKeywords($translation->getMetaKeywords());

        return $newEntity;
    }

    public function duplicateProductImage(
        ProductInterface $product,
        ProductImageInterface $image,
    ): ProductImageInterface {
        $class = $image::class;
        $newEntity = new $class();
        assert($newEntity instanceof ProductImageInterface);

        $newEntity->setOwner($product);
        $newEntity->setType($image->getType());

        assert($image->getPath() !== null);
        $binaryFile = $this->dataManager->find('sylius_shop_product_original', $image->getPath());

        $temp = tmpfile();
        fwrite($temp, (string) $binaryFile->getContent());
        fseek($temp, 0);
        /** @var array{uri: string} $metadata */
        $metadata = stream_get_meta_data($temp);
        $newEntity->setFile(new UploadedFile($metadata['uri'], $image->getPath()));
        $this->imageUploader->upload($newEntity);
        fclose($temp);

        return $newEntity;
    }

    private function generateSlug(
        string $slug,
        string $locale,
    ): string {
        $i = 0;
        do {
            if ($i === 0) {
                $newSlug = $slug . '-copy';
            } else {
                $newSlug = $slug . '-' . $i;
            }
            ++$i;
        } while (!$this->isSlugUnique($newSlug, $locale));

        return $newSlug;
    }

    private function generateProductCode(string $code): string
    {
        $i = 0;
        do {
            if ($i === 0) {
                $newCode = $code . '-copy';
            } else {
                $newCode = $code . '-copy-' . $i;
            }
            ++$i;
        } while (!$this->isProductCodeIsUnique($newCode));

        return $newCode;
    }

    private function generateProductVariantCode(string $code): string
    {
        $i = 0;
        do {
            if ($i === 0) {
                $newCode = $code . '-copy';
            } else {
                $newCode = $code . '-copy-' . $i;
            }
            ++$i;
        } while (!$this->isProductVariantCodeIsUnique($newCode));

        return $newCode;
    }

    private function isProductCodeIsUnique(string $code): bool
    {
        return count($this->productRepository->findBy(['code' => $code])) === 0;
    }

    private function isProductVariantCodeIsUnique(string $code): bool
    {
        return count($this->productVariantRepository->findBy(['code' => $code])) === 0;
    }

    private function isSlugUnique(
        string $slug,
        string $locale,
    ): bool {
        /** @var EntityRepository $repository */
        $repository = $this->productRepository;
        $count = (int) $repository->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->join('o.translations', 't')
            ->where('t.slug = :slug')
            ->andWhere('t.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getSingleScalarResult();

        return $count === 0;
    }
}
