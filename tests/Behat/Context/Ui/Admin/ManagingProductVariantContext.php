<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\ProductVariant\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ManagingProductVariantContext implements Context
{
    public function __construct(
        private ShowPageInterface            $showPage,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I duplicate the product variant
     */
    public function iDuplicateTheProductVariant()
    {
        $this->showPage->duplicateProduct();
    }

    /**
     * @Then the code field should end with :arg1
     */
    public function theCodeFieldShouldEndWith($arg1)
    {
        $code  = $this->showPage->getCodeValue();
        $parts = explode('-', $code);

        Assert::eq(end($parts), 'copy');
    }

    /**
     * @Then I should be notified that it has been successfully duplicated
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDuplicated()
    {
        $this->notificationChecker->checkNotification(
            'The product variant was successfully duplicated',
            NotificationType::success(),
        );
    }
}
