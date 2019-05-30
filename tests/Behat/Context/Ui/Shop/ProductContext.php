<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

final class ProductContext implements Context
{
	/**
	 * @var ProductRepositoryInterface
	 */
	private $productRepository;
	/**
	 * @var ChannelRepositoryInterface
	 */
	private $channelRepository;

	public function __construct(
		ProductRepositoryInterface $productRepository,
		ChannelRepositoryInterface $channelRepository
	) {
		$this->productRepository = $productRepository;
		$this->channelRepository = $channelRepository;
	}

	/**
	 * @Given /^check that the product "([^"]+)" has price "([^"]+)" on channel "([^"]+)"$/
	 */
	public function checkThatTheProductHasPriceOnChannel(string $productName, string $price, string $channelName)
	{
		$productCode = StringInflector::nameToUppercaseCode($productName);
		$channelCode = StringInflector::nameToLowercaseCode($channelName);

		$channel = $this->channelRepository->findOneByCode($channelCode);
		assert($channel instanceof ChannelInterface);

		$product = $this->productRepository->findOneByCode($productCode);
		assert($product instanceof ProductInterface);
		$variant = $product->getVariants()->first();
		assert($variant instanceof ProductVariantInterface);
		$pricing = $variant->getChannelPricingForChannel($channel);
		assert($pricing !== null);

		$price = (int) filter_var($price, FILTER_SANITIZE_NUMBER_INT);
		assert($pricing->getPrice() === $price);
	}
}
