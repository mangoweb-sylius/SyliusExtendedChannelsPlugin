<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;

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

    /**
     * @return Collection<ChannelInterface>
     */
    public function getChannels(): Collection;

    /**
     * @param array<ChannelInterface>|Collection<ChannelInterface> $channels
     */
    public function setChannels($channels): void;

    public function getFallbackLocale(): ?string;

    public function getMessageType(): ?string;

    public function setMessageType(?string $messageType): void;
}
