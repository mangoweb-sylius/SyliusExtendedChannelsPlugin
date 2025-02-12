<?php

declare(strict_types=1);

namespace MangoSylius\ExtendedChannelsPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ExternalLinkTaxonTrait
{
    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    public $externalLink = false;

    public function getExternalLink(): ?bool
    {
        return $this->externalLink;
    }

    public function setExternalLink(?bool $externalLink): void
    {
        $this->externalLink = $externalLink;
    }
}
