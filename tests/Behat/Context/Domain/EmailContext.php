<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\Checker\EmailCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class EmailContext implements Context
{
    public function __construct(private readonly EmailCheckerInterface $emailChecker)
    {
    }

    /**
     * @Then /^an email generated for (order placed by "[^"]+") should be sent to "([^"]+)"$/
     */
    public function anEmailGeneratedForOrderShouldBeSentTo(
        OrderInterface $order,
        string         $arg2,
    ): void {
        Assert::true($this->emailChecker->hasMessageTo('Your order no. ' . $order->getNumber() . ' has been successfully placed.', $arg2));
    }
}
