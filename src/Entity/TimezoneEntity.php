<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Table(name="mango_timezone")
 *
 * @ORM\Entity
 */
#[ORM\Table(name: 'mango_timezone')]
#[ORM\Entity]
class TimezoneEntity implements ResourceInterface, \Stringable
{
    /**
     * @ORM\Column(name="id", type="integer")
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    public function __construct(
        /** @ORM\Column(type="string", name="timezone_title") */
        #[ORM\Column(name: 'timezone_title', type: 'string')]
        protected string $timezoneTitle,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTimezoneTitle(): string
    {
        return $this->timezoneTitle;
    }

    public function __toString(): string
    {
        return $this->timezoneTitle;
    }
}
