<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Channel\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
	public function isSingleResourceOnPage(string $elementName)
	{
		return $this->getElement($elementName)->getValue();
	}

	public function changeBccEmail(string $bccEmail): void
	{
		$this->getElement('bccEmail')->setValue($bccEmail);
	}

	public function changePhone(string $phoneNumber): void
	{
		$this->getElement('phone')->setValue($phoneNumber);
	}

	public function changeTimezone(int $timezone): void
	{
		$this->getElement('timezone')->setValue((string) $timezone);
	}

	protected function getDefinedElements(): array
	{
		return array_merge(parent::getDefinedElements(), [
			'phone' => '#sylius_admin_channel_contactPhone',
			'bccEmail' => '#sylius_admin_channel_bccEmail',
			'timezone' => '#sylius_admin_channel_timezone',
		]);
	}
}
