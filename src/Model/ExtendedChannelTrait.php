<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;
use MangoSylius\ExtendedChannelsPlugin\Entity\TimezoneEntity;

trait ExtendedChannelTrait
{
    #[ORM\ManyToOne(targetEntity: TimezoneEntity::class)]
    private ?TimezoneEntity $timezone = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $bccEmail = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $contactPhone = null;

    public function getBccEmail(): ?string
    {
        return $this->bccEmail;
    }

    public function setBccEmail(?string $bccEmail): void
    {
        $this->bccEmail = $bccEmail;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): void
    {
        $this->contactPhone = $contactPhone;
    }

    public function getTimezone(): ?TimezoneEntity
    {
        return $this->timezone;
    }

    public function setTimezone(?TimezoneEntity $timezone): void
    {
        $this->timezone = $timezone;
    }
}
