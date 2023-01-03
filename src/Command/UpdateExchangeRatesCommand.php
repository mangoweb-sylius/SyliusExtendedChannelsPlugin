<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateExchangeRatesCommand extends Command
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/** @var ExchangeRateRepositoryInterface */
	private $exchangeRateRepository;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		ExchangeRateRepositoryInterface $exchangeRateRepository
	) {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->exchangeRateRepository = $exchangeRateRepository;

		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setName('mango:exchange-rates:update')
			->addArgument('exchangeratesUrl', InputArgument::OPTIONAL, 'URL', 'https://api.exchangeratesapi.io/latest?base=%currency%')
			->setDescription('Update exchange rates.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$io->title($this->getName() . ' started at ' . date('Y-m-d H:i:s'));

		$currencies = [];
		$exchangeRates = $this->exchangeRateRepository->findAll();
		foreach ($exchangeRates as $exchangeRate) {
			assert($exchangeRate instanceof ExchangeRateInterface);
			assert($exchangeRate->getSourceCurrency() !== null);
			$currencies[] = $exchangeRate->getSourceCurrency()->getCode();
		}

		$exchangeRatesUrl = $input->getArgument('exchangeratesUrl');
		assert(is_string($exchangeRatesUrl));

		$exchangeRatesJsons = [];
		foreach ($currencies as $currency) {
			$json = file_get_contents(str_replace('%currency%', $currency, $exchangeRatesUrl));
			if ($json === false) {
				$errorMsg = 'Missing source JSON for ' . $currency;
				$io->warning($errorMsg);
				$this->logger->warning($errorMsg);
			} else {
				$jsonArray = json_decode($json, true);
				if (count($jsonArray['rates']) === 0) {
					$errorMsg = 'Invalid JSON';
					$io->warning($errorMsg);
					$this->logger->warning($errorMsg);

					continue;
				}
				$exchangeRatesJsons[$currency] = $jsonArray['rates'];
			}
		}

		foreach ($exchangeRates as $exchangeRate) {
			assert($exchangeRate instanceof ExchangeRateInterface);

			assert($exchangeRate->getSourceCurrency() !== null);
			assert($exchangeRate->getTargetCurrency() !== null);
			$sourceCurrency = $exchangeRate->getSourceCurrency()->getCode();
			$targetCurrency = $exchangeRate->getTargetCurrency()->getCode();
			assert($sourceCurrency !== null);
			assert($targetCurrency !== null);

			if (!array_key_exists($sourceCurrency, $exchangeRatesJsons)) {
				continue;
			}
			if (!array_key_exists($targetCurrency, $exchangeRatesJsons[$sourceCurrency])) {
				$errorMsg = 'Json ' . $sourceCurrency . ' does not contain ' . $targetCurrency;
				$io->warning($errorMsg);
				$this->logger->warning($errorMsg);

				continue;
			}
			$rate = $exchangeRatesJsons[$sourceCurrency][$targetCurrency];
			if ($rate === 0) {
				$errorMsg = 'Exchange Rate = 0... skip';
				$io->warning($errorMsg);
				$this->logger->warning($errorMsg);

				continue;
			}

			$exchangeRate->setRatio($rate);
			$this->entityManager->persist($exchangeRate);

			$io->title('Update ' . $sourceCurrency . ' to ' . $targetCurrency . ' with rate ' . $rate);
		}
		$this->entityManager->flush();

		$io->success(
			$this->getName() . ' at ' . date('Y-m-d H:i:s')
		);

        return 0;
	}
}
