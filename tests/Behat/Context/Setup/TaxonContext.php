<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ExtendedChannelsPlugin\Model\ExternalLinkTaxonInterface;

final class TaxonContext implements Context
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @Given /^(this taxon) is marked as external link$/
     */
    public function thisTaxonIsMarkedAsExternalLink(ExternalLinkTaxonInterface $taxon)
    {
        $taxon->setExternalLink(true);

        $this->entityManager->persist($taxon);
        $this->entityManager->flush();
    }
}
