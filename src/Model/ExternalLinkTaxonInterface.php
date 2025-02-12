<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Model;

interface ExternalLinkTaxonInterface
{
    public function getExternalLink(): ?bool;

    public function setExternalLink(?bool $externalLink): void;
}
