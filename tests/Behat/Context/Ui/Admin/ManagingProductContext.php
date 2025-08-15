<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Product\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ManagingProductContext implements Context
{
    public function __construct(
        private readonly ShowPageInterface            $showPage,
        private readonly NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I duplicate the product
     */
    public function iDuplicateTheProduct()
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
            'The product was successfully duplicated',
            NotificationType::success(),
        );
    }
}
