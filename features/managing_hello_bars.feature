@managing_hello_bars
Feature: Managing Hello bars
    In order to display notifications and messages to customers
    As an Administrator
    I want to be able to create, update and manage Hello bars

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Creating a new Hello bar
        When I want to create a new Hello bar
        And I specify its title as "Welcome Message"
        And I specify its content as "Welcome to our store!"
        And I specify its message type as "info"
        And I add the Hello bar
        Then I should be notified that it has been successfully created
        And there should be 1 Hello bar in the registry

    @ui
    Scenario: Creating a Hello bar with specific channel
        Given the store operates on channels named "Web Store" and "Mobile Store"
        When I want to create a new Hello bar
        And I specify its title as "Mobile Welcome"
        And I specify its content as "Welcome to our mobile app!"
        And I specify its message type as "success"
        And I assign it to "Mobile Store" channel
        And I add the Hello bar
        Then I should be notified that it has been successfully created
        And the Hello bar "Mobile Welcome" should be assigned to "Mobile Store" channel

    @ui
    Scenario: Creating a Hello bar with time constraints
        When I want to create a new Hello bar
        And I specify its title as "Sale Alert"
        And I specify its content as "Limited time offer!"
        And I specify its message type as "warning"
        And I set its start date to "2023-12-01 10:00"
        And I set its end date to "2023-12-31 23:59"
        And I add the Hello bar
        Then I should be notified that it has been successfully created
        And the Hello bar "Sale Alert" should be scheduled from "2023-12-01 10:00" to "2023-12-31 23:59"

    @ui
    Scenario: Updating a Hello bar
        Given there is a Hello bar with title "Old Message"
        When I want to modify the Hello bar "Old Message"
        And I change its title to "Updated Message"
        And I change its message type to "error"
        And I save my changes
        Then I should be notified that it has been successfully uploaded
        And this Hello bar title should be "Updated Message"
        And this Hello bar message type should be "error"

    @ui
    Scenario: Deleting a Hello bar
        Given there is a Hello bar with title "Temporary Message"
        When I delete the Hello bar "Temporary Message"
        Then I should be notified that it has been successfully deleted
        And there should be 0 Hello bars in the registry

    @ui
    Scenario: Browsing Hello bars
        Given there are Hello bars with message types "success", "warning", "info", "error"
        When I want to browse Hello bars
        Then I should see 4 Hello bars in the list
        And I should see in the list message types "success", "warning", "info", "error"
