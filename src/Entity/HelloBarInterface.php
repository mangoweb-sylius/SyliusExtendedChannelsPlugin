<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

interface HelloBarInterface
{
	public function setTitle(?string $name): void;

	public function getTitle(): ?string;

	public function setContent(?string $name): void;

	public function getContent(): ?string;

	public function getStartsAt(): ?\DateTime;

	public function setStartsAt(?\DateTime $startsAt): void;

	public function getEndsAt(): ?\DateTime;

	public function setEndsAt(?\DateTime $endsAt): void;

	public function getChannels();

	public function setChannels($channels): void;

	public function getFallbackLocale(): ?string;

	public function getMessageType(): ?string;

	public function setMessageType(?string $messageType): void;
}
