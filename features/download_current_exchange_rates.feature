@download_current_exchange_rates
Feature: Download current exchange rates and update it
	As a administrator / cron
	Download current exchange rates
	Update exchange rates

	Background:
		Given the store has currency "EUR"
		And the store has currency "GBP"
		And the exchange rate of "EUR" to "GBP" is 1

	@ui
	Scenario: Run the command and download and update exchange rates
		Given I run command to update exchange rates
		And the exchange rate of "EUR" to "GBP" should be 0.88225
