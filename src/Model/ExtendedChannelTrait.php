<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;
use MangoSylius\ExtendedChannelsPlugin\Entity\TimezoneEntity;

trait ExtendedChannelTrait
{
	/**
	 * @var TimezoneEntity|null
	 * @ORM\ManyToOne(targetEntity="MangoSylius\ExtendedChannelsPlugin\Entity\TimezoneEntity")
	 */
	private $timezone;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $bccEmail;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $contactPhone;

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
