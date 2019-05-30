<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelInterface;
use MangoSylius\ExtendedChannelsPlugin\Model\ExtendedChannelTrait;
use Sylius\Component\Core\Model\Channel as SyliusChannel;

/**
 * @ORM\Table(name="sylius_channel")
 * @ORM\Entity
 */
class Channel extends SyliusChannel implements ExtendedChannelInterface
{
	use ExtendedChannelTrait;
}
