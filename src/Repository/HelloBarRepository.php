<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use MangoSylius\ExtendedChannelsPlugin\Entity\HelloBarInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class HelloBarRepository extends EntityRepository implements HelloBarRepositoryInterface
{
	/**
	 * @return HelloBarInterface[]
	 */
	public function findAvailableForChannel(ChannelInterface $channel): array
	{
		return $this->availableBuilder($channel)
			->getQuery()
			->getResult();
	}

	/**
	 * @return HelloBarInterface[]
	 */
	public function findAvailableForChannelByType(ChannelInterface $channel, string $type): array
	{
		return $this->availableBuilder($channel)
			->andWhere('o.messageType = :type')
			->setParameter('type', $type)
			->getQuery()
			->getResult();
	}

	public function availableBuilder(ChannelInterface $channel): QueryBuilder
	{
		return $this->createQueryBuilder('o')
			->andWhere(':channel MEMBER OF o.channels')
			->andWhere('o.startsAt IS NULL OR o.startsAt < :date')
			->andWhere('o.endsAt IS NULL OR o.endsAt > :date')
			->setParameter('date', new \DateTime())
			->setParameter('channel', $channel)
			->orderBy('o.startsAt', Criteria::ASC);
	}
}
