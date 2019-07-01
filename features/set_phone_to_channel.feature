@managing_channel
Feature: Set phone number for channel
	In order to add a phone number to channel settings in admin panel
	As an Administrator
	I want to set the phone number on the channel details page

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
