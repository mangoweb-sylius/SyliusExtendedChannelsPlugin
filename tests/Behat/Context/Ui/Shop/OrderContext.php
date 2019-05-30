<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderContext implements Context
{
	/**
	 * @var SharedStorageInterface
	 */
	private $sharedStorage;
	/**
	 * @var EventDispatcherInterface
	 */
	private $eventDispatcher;

	public function __construct(
		SharedStorageInterface $sharedStorage,
		EventDispatcherInterface $eventDispatcher
	) {
		$this->sharedStorage = $sharedStorage;
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @Given shop send an email after finished order
	 */
	public function shopSendAnEmailAfterFinishedOrder()
	{
		$order = $this->sharedStorage->get('order');

		$event = new GenericEvent($order);
		$this->eventDispatcher->dispatch('sylius.order.post_complete', $event);
	}
}
