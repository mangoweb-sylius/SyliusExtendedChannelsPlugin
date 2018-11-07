<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Model;

use MangoSylius\ExtendedChannelsPlugin\Entity\TimezoneEntity;

interface ExtendedChannelInterface
{
	public function getBccEmail(): ?string;

	public function setBccEmail(?string $bccEmail): void;

	public function getContactPhone(): ?string;

	public function setContactPhone(?string $contactPhone): void;

	public function getTimezone(): ?TimezoneEntity;

	public function setTimezone(?TimezoneEntity $timezone): void;
}
