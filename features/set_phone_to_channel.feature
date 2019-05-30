@managing_channel
Feature: Set phone number to channel
	Add a phone number to the channel in administration (for email, footer, etc.)
	As an Administrator
	I want to set a phone number to the channel

	Background:
		Given the store operates on a channel named "Mango Channel"
		And I am logged in as an administrator

	@ui
	Scenario: Set phone number to channel
		Given I want to modify a channel "Mango Channel"
		When I change its phone to "777 888 999"
		And I save my changes
		Then I should be notified that it has been successfully edited
		And this channel phone should be "777 888 999"
