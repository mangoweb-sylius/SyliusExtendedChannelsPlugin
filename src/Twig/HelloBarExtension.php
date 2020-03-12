<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Twig;

use MangoSylius\ExtendedChannelsPlugin\Repository\HelloBarRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HelloBarExtension extends AbstractExtension
{
	/**
	 * @var ChannelContextInterface
	 */
	private $channelContext;
	/**
	 * @var HelloBarRepositoryInterface
	 */
	private $helloBarRepository;

	public function __construct(
		ChannelContextInterface $channelContext,
		HelloBarRepositoryInterface $helloBarRepository
	) {
		$this->channelContext = $channelContext;
		$this->helloBarRepository = $helloBarRepository;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('mangoweb_sylius_available_hello_bars', [$this, 'getAvailableHelloBars']),
			new TwigFunction('mangoweb_sylius_available_hello_bars_by_type', [$this, 'getAvailableHelloBarsByType']),
		];
	}

	public function getAvailableHelloBars(): array
	{
		return $this->helloBarRepository->findAvailableForChannel($this->channelContext->getChannel());
	}

	public function getAvailableHelloBarsByType(string $type): array
	{
		return $this->helloBarRepository->findAvailableForChannelByType($this->channelContext->getChannel(), $type);
	}
}
