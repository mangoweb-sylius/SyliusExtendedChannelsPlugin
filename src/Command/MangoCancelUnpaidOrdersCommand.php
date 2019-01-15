<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Command;

use MangoSylius\ExtendedChannelsPlugin\Service\MangoUnpaidOrdersStateUpdater;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MangoCancelUnpaidOrdersCommand extends ContainerAwareCommand
{
	/** @var MangoUnpaidOrdersStateUpdater */
	private $unpaidCartsStateUpdater;

	public function __construct(
		MangoUnpaidOrdersStateUpdater $unpaidCartsStateUpdater
	) {
		$this->unpaidCartsStateUpdater = $unpaidCartsStateUpdater;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure(): void
	{
		$this
			->setName('mango:cancel-unpaid-orders')
			->setDescription(
				'Removes order that have been unpaid for a configured period. Configuration parameters - sylius_order.order_expiration_period, sylius_order.expiration_method_codes'
			);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$expirationTime = $this->getContainer()->getParameter('sylius_order.order_expiration_period');
		$methodCodes = $this->getContainer()->getParameter('sylius_order.expiration_method_codes');

		$output->writeln(sprintf(
			'Command will cancel orders that have been unpaid for <info>%s</info> for payment method with codes <info>%s</info>.',
			$expirationTime,
			implode(', ', $methodCodes)
		));

		$this->unpaidCartsStateUpdater->cancel();

		$this->getContainer()->get('sylius.manager.order')->flush();
	}
}
