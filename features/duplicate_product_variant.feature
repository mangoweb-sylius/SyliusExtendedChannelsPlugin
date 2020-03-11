@duplicate_product_variant
Feature: Duplicate product variant
	In order to be able to duplicate product variant
	As an Administrator
	I want to have an appropriate button on variant list view

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a "Wyborowa Vodka" configurable product
		And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
		And I am logged in as an administrator

	@ui
	Scenario: Being able to duplicate product variant
		When I want to view all variants of this product
		And I duplicate the product variant
		Then the code field should end with "-copy"
		And I should be notified that it has been successfully duplicated
