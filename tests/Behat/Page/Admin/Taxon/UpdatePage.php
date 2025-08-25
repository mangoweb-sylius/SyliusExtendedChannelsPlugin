<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Taxon;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
	public function markTaxonAsExternalLink(): void
	{
		$this->getElement('external_link_checkbox')->setValue(true);
	}

	public function unmarkTaxonAsExternalLink(): void
	{
		$this->getElement('external_link_checkbox')->setValue(false);
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			'external_link_checkbox' => '#sylius_admin_taxon_externalLink',
		]);
	}

	public function isSingleResourceOnPage(string $elementName)
	{
		return $this->getElement($elementName)->getValue();
	}
}
