<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use MangoSylius\ExtendedChannelsPlugin\Model\ExternalLinkTaxonInterface;

final class TaxonContext implements Context
{
	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	public function __construct(
		ObjectManager $objectManager
	) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @Given /^(this taxon) is marked as external link$/
	 */
	public function thisTaxonIsMarkedAsExternalLink(ExternalLinkTaxonInterface $taxon)
	{
		$taxon->setExternalLink(true);

		$this->objectManager->persist($taxon);
		$this->objectManager->flush();
	}
}
