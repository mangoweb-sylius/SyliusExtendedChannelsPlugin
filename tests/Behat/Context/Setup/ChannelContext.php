<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Entity\Channel;

final class ChannelContext implements Context
{
    public function __construct(
        private readonly EntityManagerInterface $channelManager,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly FactoryInterface $channelFactory,
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

    /**
     * @Given the store operates on channels named :firstChannelName and :secondChannelName
     */
    public function theStoreOperatesOnChannelsNamedAnd(string $firstChannelName, string $secondChannelName): void
    {
        $this->createChannel($firstChannelName);
        $this->createChannel($secondChannelName);
    }

    private function createChannel(string $channelName): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNew();
        $channel->setCode(strtoupper(str_replace(' ', '_', $channelName)));
        $channel->setName($channelName);
        $channel->setBaseCurrency($this->sharedStorage->get('currency'));
        $channel->setDefaultLocale($this->sharedStorage->get('locale'));

        $this->channelManager->persist($channel);
        $this->channelManager->flush();

        $this->sharedStorage->set($channelName, $channel);

        return $channel;
    }
}
