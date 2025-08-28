<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use MangoSylius\ExtendedChannelsPlugin\Model\ExternalLinkTaxonInterface;
use MangoSylius\ExtendedChannelsPlugin\Model\ExternalLinkTaxonTrait;
use Sylius\Component\Core\Model\Taxon as SyliusTaxon;

#[ORM\Table(name: "sylius_taxon")]
#[ORM\Entity]
class Taxon extends SyliusTaxon implements ExternalLinkTaxonInterface
{
	use ExternalLinkTaxonTrait;
}
