<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Service;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductDuplicatorInterface
{
	public function duplicateProduct(ProductInterface $oldEntity): ProductInterface;

	public function duplicateProductVariant(ProductInterface $product, ProductVariantInterface $oldEntity): ProductVariantInterface;
}
