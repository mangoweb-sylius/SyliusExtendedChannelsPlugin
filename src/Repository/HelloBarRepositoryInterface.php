<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Repository;

use MangoSylius\ExtendedChannelsPlugin\Entity\HelloBarInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface HelloBarRepositoryInterface extends RepositoryInterface
{
	/**
	 * @return HelloBarInterface[]
	 */
	public function findAvailableForChannel(ChannelInterface $channel): array;

	/**
	 * @return HelloBarInterface[]
	 */
	public function findAvailableForChannelByType(ChannelInterface $channel, string $type): array;
}
