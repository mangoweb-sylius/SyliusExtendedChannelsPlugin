<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\Taxon\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxonContext implements Context
{
    public function __construct(private readonly UpdatePageInterface $updatePage)
    {
    }

    /**
     * @When /^I mark (this taxon) as external link$/
     */
    public function iMarkTaxonAsExternalLink()
    {
        $this->updatePage->markTaxonAsExternalLink();
    }

    /**
     * @Then /^(this taxon) should be marked as external link$/
     */
    public function thisTaxonShouldBeMarkedAsExternalLink()
    {
        Assert::true((bool)$this->updatePage->isSingleResourceOnPage('external_link_checkbox'));
    }

    /**
     * @When /^I unmark (this taxon) as external link$/
     */
    public function iUnmarkTaxonAsExternalLink()
    {
        $this->updatePage->unmarkTaxonAsExternalLink();
    }

    /**
     * @Then /^(this taxon) should be unmarked as external link$/
     */
    public function thisTaxonShouldBeUnmarkedAsExternalLink()
    {
        Assert::false((bool)$this->updatePage->isSingleResourceOnPage('external_link_checkbox'));
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }
}
