<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
	public function getRouteName(): string
	{
		return 'sylius_admin_order_show';
	}

	public function resendOrderEmail(): void
	{
		$this->getElement('resend-order-button')->click();
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			'resend-order-button' => '.six.wide.right.aligned.column .ui.blue.labeled.icon.button',
		]);
	}
}
