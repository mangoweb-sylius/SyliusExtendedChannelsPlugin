<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Webmozart\Assert\Assert;

final class EmailContext implements Context
{
	/**
	 * @var EmailCheckerInterface
	 */
	private $emailChecker;

	public function __construct(
		EmailCheckerInterface $emailChecker
	) {
		$this->emailChecker = $emailChecker;
	}

	/**
	 * @Then /^an email generated for (order placed by "[^"]+") should be sent to "([^"]+)"$/
	 */
	public function anEmailGeneratedForOrderShouldBeSentTo(OrderInterface $order, string $arg2): void
	{
		Assert::true($this->emailChecker->hasMessageTo('Your order no. ' . $order->getNumber() . ' has been successfully placed.', $arg2));
	}
}
