<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Sylius\Component\Resource\Model\TranslationInterface;

interface HelloBarTranslationInterface extends TranslationInterface
{
	public function getTitle(): ?string;

	public function setTitle(?string $title): void;

	public function getContent(): ?string;

	public function setContent(?string $content): void;
}
