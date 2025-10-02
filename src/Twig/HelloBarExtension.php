<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Twig;

use MangoSylius\ExtendedChannelsPlugin\Entity\HelloBarInterface;
use MangoSylius\ExtendedChannelsPlugin\Repository\HelloBarRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HelloBarExtension extends AbstractExtension
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly HelloBarRepositoryInterface $helloBarRepository,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('mangoweb_sylius_available_hello_bars', $this->getAvailableHelloBars(...)),
            new TwigFunction('mangoweb_sylius_available_hello_bars_by_type', $this->getAvailableHelloBarsByType(...)),
        ];
    }

    /**
     * @return HelloBarInterface[]
     */
    public function getAvailableHelloBars(): array
    {
        return $this->helloBarRepository->findAvailableForChannel($this->channelContext->getChannel());
    }

    /**
     * @return HelloBarInterface[]
     */
    public function getAvailableHelloBarsByType(string $type): array
    {
        return $this->helloBarRepository->findAvailableForChannelByType($this->channelContext->getChannel(), $type);
    }
}
