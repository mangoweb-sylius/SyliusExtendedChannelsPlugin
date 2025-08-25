<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Taxon;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
	public function markTaxonAsExternalLink(): void;

	public function unmarkTaxonAsExternalLink(): void;

	public function isSingleResourceOnPage(string $elemName);
}
