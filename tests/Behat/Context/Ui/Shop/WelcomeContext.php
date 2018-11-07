<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Shop\WelcomePageInterface;
use Webmozart\Assert\Assert;

final class WelcomeContext implements Context
{
	/**
	 * @var WelcomePageInterface
	 */
	private $staticWelcomePage;

	/**
	 * @var WelcomePageInterface
	 */
	private $dynamicWelcomePage;

	public function __construct(WelcomePageInterface $staticWelcomePage, WelcomePageInterface $dynamicWelcomePage)
	{
		$this->staticWelcomePage = $staticWelcomePage;
		$this->dynamicWelcomePage = $dynamicWelcomePage;
	}

	public function customerWithUnknownNameVisitsStaticWelcomePage(): void
	{
		$this->staticWelcomePage->open();
	}

	public function namedCustomerVisitsStaticWelcomePage(string $name): void
	{
		$this->staticWelcomePage->open(['name' => $name]);
	}

	public function theyShouldBeStaticallyGreetedWithGreeting(string $greeting): void
	{
		Assert::same($this->staticWelcomePage->getGreeting(), $greeting);
	}

	public function customerWithUnknownNameVisitsDynamicWelcomePage(): void
	{
		$this->dynamicWelcomePage->open();
	}

	public function namedCustomerVisitsDynamicWelcomePage(string $name): void
	{
		$this->dynamicWelcomePage->open(['name' => $name]);
	}

	public function theyShouldBeDynamicallyGreetedWithGreeting(string $greeting): void
	{
		Assert::same($this->dynamicWelcomePage->getGreeting(), $greeting);
	}
}
