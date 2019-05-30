@managing_channel
Feature: Set bcc email to channel
	Add a bcc email to the channel in administration for email send copy of email to it.
	As an Administrator
	I want to set bcc email to the channel

	Background:
		Given the store operates on a channel named "Mango Channel"
		And I am logged in as an administrator

	@ui
	Scenario: Set bcc email to channel
		Given I want to modify a channel "Mango Channel"
		When I change its bcc email to "sylius@mangoweb.cz"
		And I save my changes
		Then I should be notified that it has been successfully edited
		And this channel bcc email should be "sylius@mangoweb.cz"
