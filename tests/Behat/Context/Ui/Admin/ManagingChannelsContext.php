<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use App\Entity\Channel;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ExtendedChannelsPlugin\Entity\TimezoneEntity;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Channel\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
	/**
	 * @var UpdatePageInterface
	 */
	private $updatePage;
	/**
	 * @var SharedStorageInterface
	 */
	private $sharedStorage;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(
		UpdatePageInterface $updatePage,
		SharedStorageInterface $sharedStorage,
		EntityManagerInterface $entityManager
	) {
		$this->updatePage = $updatePage;
		$this->sharedStorage = $sharedStorage;
		$this->entityManager = $entityManager;
	}

	/**
	 * @Given there is a timezone :timezone
	 */
	public function thereIsATimezone($timezone)
	{
		$timezone = new TimezoneEntity($timezone);

		$this->entityManager->persist($timezone);
		$this->entityManager->flush();

		$this->sharedStorage->set('timezone', $timezone);
	}

	/**
	 * @When I change its timezone to :timezone
	 */
	public function iChangeItsTimezoneTo(string $timezone): void
	{
		$timezoneEntity = $this->findTimezoneEntityByName($timezone);
		\assert($timezoneEntity !== null);
		$this->updatePage->changeTimezone($timezoneEntity->getId());
	}

	/**
	 * @Then /^(this channel) timezone should be "([^"]+)"$/
	 */
	public function thisChannelTimezoneShouldBe(Channel $channel, string $timezone): void
	{
		$this->iWantToModifyChannel($channel);
		$timezoneEntity = $this->findTimezoneEntityByName($timezone);
		\assert($timezoneEntity !== null);
		Assert::eq($this->updatePage->isSingleResourceOnPage('timezone'), (string) $timezoneEntity->getId());
	}

	/**
	 * @When I change its bcc email to :bccEmail
	 */
	public function iChangeItsBccEmailTo(string $bccEmail): void
	{
		$this->updatePage->changeBccEmail($bccEmail);
	}

	/**
	 * @Then /^(this channel) bcc email should be "([^"]+)"$/
	 */
	public function thisChannelBccEmailShouldBe(Channel $channel, string $bccEmail): void
	{
		$this->iWantToModifyChannel($channel);

		Assert::eq($this->updatePage->isSingleResourceOnPage('bccEmail'), $bccEmail);
	}

	/**
	 * @When I change its phone to :phoneNumber
	 */
	public function iChangeItsPhoneTo(string $phoneNumber): void
	{
		$this->updatePage->changePhone($phoneNumber);
	}

	/**
	 * @Then /^(this channel) phone should be "([^"]+)"$/
	 */
	public function thisChannelPhoneShouldBe(Channel $channel, string $phoneNumber): void
	{
		$this->iWantToModifyChannel($channel);

		Assert::eq($this->updatePage->isSingleResourceOnPage('phone'), $phoneNumber);
	}

	/**
	 * @Given I want to modify a channel :channel
	 * @Given /^I want to modify (this channel)$/
	 */
	public function iWantToModifyChannel(Channel $channel): void
	{
		$this->updatePage->open(['id' => $channel->getId()]);
	}

	/**
	 * @When I save my changes
	 * @When I try to save my changes
	 */
	public function iSaveMyChanges(): void
	{
		$this->updatePage->saveChanges();
	}

	private function findTimezoneEntityByName(string $name): ?TimezoneEntity
	{
		return $this->entityManager->getRepository(TimezoneEntity::class)->findOneBy(['timezoneTitle' => $name]);
	}
}
