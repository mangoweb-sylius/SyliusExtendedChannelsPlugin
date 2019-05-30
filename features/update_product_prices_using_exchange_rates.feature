@update_product_prices_using_exchange_rates
Feature: Update product prices using exchange rates
	As a administrator / cron
	Update product prices using exchange rates

	Background:
		Given the store has currency "EUR"
		And the store has currency "GBP"
		And the exchange rate of "EUR" to "GBP" is 1.2
		And the store operates on a channel named "Web-EU" in "EUR" currency
		And the store operates on another channel named "Web-GB" in "GBP" currency
		And the store has a product "Screwdriver" priced at "€10.00" in "Web-EU" channel
		And this product is also priced at "£10.00" in "Web-GB" channel


	@ui
	Scenario: Run comman and check prices
		Given I run command to update product prices on channels "Web-EU" and "Web-GB"
		And check that the product "Screwdriver" has price "€10.00" on channel "Web-EU"
		And check that the product "Screwdriver" has price "€12.00" on channel "Web-GB"
