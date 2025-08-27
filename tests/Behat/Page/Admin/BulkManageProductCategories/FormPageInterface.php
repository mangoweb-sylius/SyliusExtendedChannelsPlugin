<?php

declare(strict_types=1);

namespace Tests\MangoSylius\ExtendedChannelsPlugin\Behat\Page\Admin\BulkManageProductCategories;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface FormPageInterface extends PageInterface
{
    public function setMainTaxon(string $taxonName): void;

    public function setMainTaxonAction(string $action): void;

    public function selectTaxon(string $taxonName): void;

    public function setTaxonsAction(string $action): void;

    public function saveChanges(): void;
}