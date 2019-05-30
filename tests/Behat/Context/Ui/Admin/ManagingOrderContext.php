<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Order\ShowPageInterface;

final class ManagingOrderContext implements Context
{
	/**
	 * @var ShowPageInterface
	 */
	private $showPage;
	/**
	 * @var NotificationCheckerInterface
	 */
	private $notificationChecker;

	public function __construct(
		ShowPageInterface $showPage,
		NotificationCheckerInterface $notificationChecker
	) {
		$this->showPage = $showPage;
		$this->notificationChecker = $notificationChecker;
	}

	/**
	 * @When I resend the order email
	 */
	public function iResendTheOrderEmail(): void
	{
		$this->showPage->resendOrderEmail();
	}

	/**
	 * @When I should be notified that the email was sent successfully
	 */
	public function iShouldBeNotifiedThatTheEmailWasSentSuccessfully(): void
	{
		$this->notificationChecker->checkNotification(
			'Email sucessfully sent',
			NotificationType::success()
		);
	}
}
