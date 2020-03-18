@mark_taxon_as_external_link
Feature: Mark taxon as external link
	In order to mark taxon as external link in taxon settings in admin panel
	As an Administrator
	I want to mark taxon as external on the taxon details page

	Background:
		Given the store is available in "English (United States)"
		And the store classifies its products as "T-Shirts" and "Accessories"
		And I am logged in as an administrator

	@ui
	Scenario: Mark taxon as external link
		Given I want to modify the "T-Shirts" taxon
		When I mark this taxon as external link
		And I save my changes
		Then I should be notified that it has been successfully edited
		And this taxon should be marked as external link

	@ui
	Scenario: Unmark taxon as external link
		Given I want to modify the "Accessories" taxon
		And this taxon is marked as external link
		When I unmark this taxon as external link
		And I save my changes
		Then I should be notified that it has been successfully edited
		And this taxon should be unmarked as external link
