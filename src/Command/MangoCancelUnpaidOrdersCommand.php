<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ExtendedChannelsPlugin\Service\MangoUnpaidOrdersStateUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MangoCancelUnpaidOrdersCommand extends Command
{
    /**
     * @param array<string> $expirationMethodCodes
     */
    public function __construct(
        private readonly MangoUnpaidOrdersStateUpdater $unpaidCartsStateUpdater,
        private readonly EntityManagerInterface $entityManager,
        private readonly string $expirationPeriod,
        private readonly array $expirationMethodCodes,
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->setName('mango:cancel-unpaid-orders')
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameters - sylius_order.order_expiration_period, sylius_order.expiration_method_codes',
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $expirationTime = $this->expirationPeriod;
        $methodCodes = $this->expirationMethodCodes;

        $output->writeln(sprintf(
            'Command will cancel orders that have been unpaid for <info>%s</info> for payment method with codes <info>%s</info>.',
            $expirationTime,
            implode(', ', $methodCodes),
        ));

        $this->unpaidCartsStateUpdater->cancel();

        $this->entityManager->flush();

        return 0;
    }
}
