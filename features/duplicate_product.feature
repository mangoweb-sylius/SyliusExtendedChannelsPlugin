@duplicate_product
Feature: Duplicate product and its variants
	In order to be able to duplicate product and its variants
	As an Administrator
	I want to have an appropriate button on Product update view in menu

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a product "Dice Brewing"
		And I am logged in as an administrator

	@ui
	Scenario: Being able to duplicate product and its variants
		When I want to modify the "Dice Brewing" product
		And I duplicate the product
		Then the code field should end with "-copy"
		And I should be notified that it has been successfully duplicated
