<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Entity\Channel;

final class ChannelContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $channelManager,
        private readonly SharedStorageInterface $sharedStorage,
    ) {
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
