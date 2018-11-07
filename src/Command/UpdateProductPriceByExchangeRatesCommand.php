<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Currency\Converter\CurrencyConverter;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateProductPriceByExchangeRatesCommand extends Command
{
	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var LoggerInterface */
	private $logger;

	/** @var ExchangeRateRepositoryInterface */
	private $exchangeRateRepository;

	/** @var ProductVariantRepositoryInterface */
	private $productVariantRepository;

	/** @var ChannelRepositoryInterface */
	private $channelRepository;

	/** @var CurrencyConverter */
	private $currencyConverter;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		ExchangeRateRepositoryInterface $exchangeRateRepository,
		ProductVariantRepositoryInterface $productVariantRepository,
		ChannelRepositoryInterface $channelRepository,
		CurrencyConverter $currencyConverter
	) {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->exchangeRateRepository = $exchangeRateRepository;
		$this->productVariantRepository = $productVariantRepository;
		$this->channelRepository = $channelRepository;
		$this->currencyConverter = $currencyConverter;

		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setName('mango:product:update-price')
			->addArgument('sourceChannel', InputArgument::REQUIRED, 'Source Channel')
			->addArgument('targetChannel', InputArgument::REQUIRED, 'Target Channel')
			->setDescription('Update product prices by exchange rates.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$io = new SymfonyStyle($input, $output);
		$io->title($this->getName() . ' started at ' . date('Y-m-d H:i:s'));

		$sourceChannelParam = $input->getArgument('sourceChannel');
		$targetChannelParam = $input->getArgument('targetChannel');

		assert(is_string($sourceChannelParam));
		assert(is_string($targetChannelParam));

		$sourceChannel = $this->channelRepository->findOneByCode($sourceChannelParam);
		$targetChannel = $this->channelRepository->findOneByCode($targetChannelParam);

		if ($sourceChannel === null || $targetChannel === null) {
			$errorMsg = 'Channel not found.';
			$this->logger->error($errorMsg);
			$io->error($errorMsg);

			return;
		}
		assert($sourceChannel instanceof ChannelInterface);
		assert($targetChannel instanceof ChannelInterface);
		assert($sourceChannel->getBaseCurrency() !== null);
		assert($targetChannel->getBaseCurrency() !== null);

		$sourceCurrency = $sourceChannel->getBaseCurrency()->getCode();
		$targetCurrency = $targetChannel->getBaseCurrency()->getCode();

		assert($sourceCurrency !== null);
		assert($targetCurrency !== null);

		if ($sourceCurrency !== $targetCurrency) {
			$exchangeRate = $this->exchangeRateRepository->findOneWithCurrencyPair($sourceCurrency, $targetCurrency);

			if ($exchangeRate === null) {
				$errorMsg = 'Exchange Rate not found for currencies ' . $sourceCurrency . ' and ' . $targetCurrency;
				$this->logger->error($errorMsg);
				$io->error($errorMsg);

				return;
			}
			assert($exchangeRate instanceof ExchangeRateInterface);
			if ($exchangeRate->getRatio() === null || $exchangeRate->getRatio() === 0) {
				$errorMsg = 'Exchange Rate is 0.';
				$this->logger->error($errorMsg);
				$io->error($errorMsg);

				return;
			}
		}

		$productVariants = $this->productVariantRepository->findAll();

		$variantsCount = count($productVariants);

		foreach ($productVariants as $variant) {
			$io->newLine(1);
			$io->progressStart($variantsCount);
			$io->progressAdvance();

			assert($variant instanceof ProductVariantInterface);
			$sourcePricing = $variant->getChannelPricingForChannel($sourceChannel);
			$targetPricing = $variant->getChannelPricingForChannel($targetChannel);
			if ($sourcePricing === null) {
				$io->warning('Missing source pricing for variant ID ' . $variant->getId());

				continue;
			}
			if ($targetPricing === null) {
				$io->warning('Missing source pricing for variant ID ' . $variant->getId());

				continue;
			}
			assert($sourcePricing->getPrice() !== null);
			$targetPricing->setPrice($this->currencyConverter->convert(
				$sourcePricing->getPrice(), $sourceCurrency, $targetCurrency
				));
			if ($sourcePricing->getOriginalPrice() !== null && $sourcePricing->getOriginalPrice() > 0) {
				$targetPricing->setOriginalPrice($this->currencyConverter->convert(
					$sourcePricing->getOriginalPrice(), $sourceCurrency, $targetCurrency
				));
			}

			$this->entityManager->persist($variant);
		}
		$this->entityManager->flush();

		$io->newLine(3);
		$io->success(
			$this->getName() . ' at ' . date('Y-m-d H:i:s')
		);
	}
}
