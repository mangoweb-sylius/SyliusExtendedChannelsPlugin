<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Entity\Channel;

final class ChannelContext implements Context
{
	/**
	 * @var EntityManagerInterface
	 */
	private $channelManager;
	/**
	 * @var SharedStorageInterface
	 */
	private $sharedStorage;

	public function __construct(
		EntityManagerInterface $channelManager,
		SharedStorageInterface $sharedStorage
	) {
		$this->channelManager = $channelManager;
		$this->sharedStorage = $sharedStorage;
	}

	/**
	 * @Given the channel has bcc email :bccEmail
	 */
	public function theChannelHasBccEmail(string $bccEmail): void
	{
		/** @var Channel $channel */
		$channel = $this->sharedStorage->get('channel');
		$channel->setBccEmail($bccEmail);

		$this->channelManager->persist($channel);
		$this->channelManager->flush();

		$this->sharedStorage->set('channel', $channel);
	}
}
