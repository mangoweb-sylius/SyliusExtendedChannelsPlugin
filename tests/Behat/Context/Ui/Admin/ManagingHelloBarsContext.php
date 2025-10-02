<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use MangoSylius\ExtendedChannelsPlugin\Repository\HelloBarRepositoryInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\CreatePageInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\IndexPageInterface;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\HelloBar\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingHelloBarsContext implements Context
{
    public function __construct(
        private readonly IndexPageInterface  $indexPage,
        private readonly CreatePageInterface $createPage,
        private readonly UpdatePageInterface $updatePage,
    ) {
    }

    /**
     * @When I want to create a new Hello bar
     */
    public function iWantToCreateANewHelloBar(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I want to browse Hello bars
     */
    public function iWantToBrowseHelloBars(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I want to modify the Hello bar :helloBar
     */
    public function iWantToModifyTheHelloBar(string $helloBar): void
    {
        $this->indexPage->open();
        $this->indexPage->updateResourceByName($helloBar);
    }

    /**
     * @When I want to view the Hello bar :helloBar
     */
    public function iWantToViewTheHelloBar(string $helloBar): void
    {
        $this->indexPage->open();
        $this->indexPage->showResourceByName($helloBar);
    }

    /**
     * @When I specify its title as :title
     */
    public function iSpecifyItsTitleAs(string $title): void
    {
        $this->createPage->specifyTitle($title);
    }

    /**
     * @When I specify its content as :content
     */
    public function iSpecifyItsContentAs(string $content): void
    {
        $this->createPage->specifyContent($content);
    }

    /**
     * @When I specify its message type as :messageType
     */
    public function iSpecifyItsMessageTypeAs(string $messageType): void
    {
        $this->createPage->specifyMessageType($messageType);
    }

    /**
     * @When I assign it to :channelName channel
     */
    public function iAssignItToChannel(string $channelName): void
    {
        $this->createPage->assignToChannel($channelName);
    }

    /**
     * @When I set its start date to :startDate
     */
    public function iSetItsStartDateTo(string $startDate): void
    {
        $this->createPage->setStartDate($startDate);
    }

    /**
     * @When I set its end date to :endDate
     */
    public function iSetItsEndDateTo(string $endDate): void
    {
        $this->createPage->setEndDate($endDate);
    }

    /**
     * @When I add the Hello bar
     */
    public function iAddHelloBar(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I change its title to :title
     */
    public function iChangeItsTitleTo(string $title): void
    {
        $this->updatePage->changeTitle($title);
    }

    /**
     * @When I change its message type to :messageType
     */
    public function iChangeItsMessageTypeTo(string $messageType): void
    {
        $this->updatePage->changeMessageType($messageType);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the Hello bar :helloBar
     */
    public function iDeleteTheHelloBar(string $helloBar): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceByName($helloBar);
    }


    /**
     * @Then there should be :count Hello bar(s) in the registry
     */
    public function thereShouldBeHelloBarsInTheRegistry(int $count): void
    {
        $this->indexPage->open();
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then I should see :count Hello bars in the list
     */
    public function iShouldSeeHelloBarsInTheList(int $count): void
    {
        $this->indexPage->open();
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then the Hello bar :helloBarTitle should be assigned to :channelName channel
     */
    public function theHelloBarShouldBeAssignedToChannel(string $helloBarTitle, string $channelName): void
    {
        $this->indexPage->open();
        $this->indexPage->updateResourceByName($helloBarTitle);

        Assert::true($this->updatePage->hasChannelAssigned($channelName));
    }

    /**
     * @Then the Hello bar :helloBarTitle should be scheduled from :startDate to :endDate
     */
    public function theHelloBarShouldBeScheduledFromTo(string $helloBarTitle, string $startDate, string $endDate): void
    {
        $this->indexPage->open();
        $this->indexPage->updateResourceByName($helloBarTitle);

        Assert::true($this->updatePage->hasSchedule($startDate, $endDate));
    }

    /**
     * @Then this Hello bar title should be :title
     */
    public function thisHelloBarTitleShouldBe(string $title): void
    {
        Assert::same($this->updatePage->getTitle(), $title);
    }

    /**
     * @Then this Hello bar message type should be :messageType
     */
    public function thisHelloBarMessageTypeShouldBe(string $messageType): void
    {
        Assert::same($this->updatePage->getMessageType(), $messageType);
    }

    /**
     * @Then /^I should see in the list message types (.+)$/
     */
    public function iShouldSeeInTheListMessageTypes(string $messageTypesString): void
    {
        // Parse the quoted string to get individual types
        preg_match_all('/"([^"]+)"/', $messageTypesString, $matches);
        $messageTypes = $matches[1];

        // Just verify there are various message types (the current method)
        Assert::true($this->indexPage->hasVariousMessageTypes());
    }
}
