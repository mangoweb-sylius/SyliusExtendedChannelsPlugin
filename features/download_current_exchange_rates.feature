@download_current_exchange_rates
Feature: Download current exchange rates and update it
	In order to update local exchange rates by live exchange rates
	As an Administrator
	Download current exchange rates and update it

	Background:
		Given the store has currency "EUR"
		And the store has currency "GBP"
		And the exchange rate of "EUR" to "GBP" is 1

	@ui
	Scenario: Run the command and download and update exchange rates
		Given I update exchange rates
		Then the exchange rate of "EUR" to "GBP" should be 0.88225
