<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Webmozart\Assert\Assert;

final class ExchangeRatesContext implements Context
{
    public function __construct(private readonly ExchangeRateRepositoryInterface $exchangeRateRepository)
    {
    }

    /**
     * @Given the exchange rate of :currencyCode1 to :currencyCode2 should be :ration
     */
    public function theExchangeRateOfToShouldBe(
        string $currencyCode1,
        string $currencyCode2,
        float  $ration,
    ) {
        $exchangeRate = $this->exchangeRateRepository->findOneWithCurrencyPair($currencyCode1, $currencyCode2);
        assert($exchangeRate !== null);
        Assert::true($exchangeRate->getRatio() === $ration);
    }
}
