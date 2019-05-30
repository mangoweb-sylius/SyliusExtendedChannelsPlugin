<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
	public function isSingleResourceOnPage(string $elementName);

	public function changeBccEmail(string $bccEmail): void;

	public function changePhone(string $phoneNumber): void;

	public function changeTimezone(int $timezone): void;
}
