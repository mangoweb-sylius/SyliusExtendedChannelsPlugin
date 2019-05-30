<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
	public function resendOrderEmail(): void;
}
