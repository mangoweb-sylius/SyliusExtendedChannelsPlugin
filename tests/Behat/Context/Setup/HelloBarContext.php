<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use MangoSylius\ExtendedChannelsPlugin\Entity\HelloBar;
use MangoSylius\ExtendedChannelsPlugin\Entity\HelloBarInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final readonly class HelloBarContext implements Context
{
    public function __construct(
        private FactoryInterface    $helloBarFactory,
        private RepositoryInterface $helloBarRepository,
        private ObjectManager       $objectManager,
    )
    {
    }

    /**
     * @Given there is a Hello bar with title :title
     */
    public function thereIsAHelloBarWithTitle(string $title): void
    {
        $this->createHelloBar($title, 'Default content', 'info');
    }

    /**
     * @Given there is a Hello bar with title :title and content :content
     */
    public function thereIsAHelloBarWithTitleAndContent(string $title, string $content): void
    {
        $this->createHelloBar($title, $content, 'info');
    }

    /**
     * @Given /^there are Hello bars with message types (.+)$/
     */
    public function thereAreHelloBarsWithDifferentTypes(string $messageTypesString): void
    {
        preg_match_all('/"([^"]+)"/', $messageTypesString, $matches);
        $messageTypes = $matches[1];

        foreach ($messageTypes as $i => $messageType) {
            $this->createHelloBar("Message {$i} ({$messageType})", "Content for {$messageType}", $messageType);
        }
    }

    private function createHelloBar(string $title, string $content, string $messageType, ?ChannelInterface $channel = null): void
    {
        $helloBar = $this->helloBarFactory->createNew();
        assert($helloBar instanceof HelloBarInterface);
        $helloBar->setCurrentLocale('en_US');
        $helloBar->setFallbackLocale('en_US');
        $helloBar->setTitle($title);
        $helloBar->setContent($content);
        $helloBar->setMessageType($messageType);

        if ($channel) {
            $helloBar->getChannels()->add($channel);
        }

        $this->helloBarRepository->add($helloBar);
        $this->objectManager->flush();

    }
}
