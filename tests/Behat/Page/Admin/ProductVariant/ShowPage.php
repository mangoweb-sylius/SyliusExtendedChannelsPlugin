<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\ProductVariant;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
	public function getRouteName(): string
	{
		return 'sylius_admin_product_update';
	}

	public function getCodeValue(): string
	{
		return (string) $this->getElement('code')->getValue();
	}

	public function duplicateProduct(): void
	{
		$this->getElement('duplicate-button')->click();
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			'code' => '#sylius_product_variant_code',
			'duplicate-button' => '.item .ui.labeled.icon.button.primary',
		]);
	}
}
