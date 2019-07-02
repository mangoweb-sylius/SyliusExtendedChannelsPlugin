@managing_channel
Feature: Set timezone for channel
	In order to add a timezone to channel settings in admin panel
	As an Administrator
	I want to set the timezone on the channel details page

	Background:
		Given the store operates on a channel named "Mango Channel"
		And there is a timezone "Europe/Prague"
		And I am logged in as an administrator

	@ui
	Scenario: Set timezone to channel
		Given I want to modify a channel "Mango Channel"
		When I change its timezone to "Europe/Prague"
		And I save my changes
		Then I should be notified that it has been successfully edited
		And this channel timezone should be "Europe/Prague"
