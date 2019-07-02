@cancel_unpaid_orders_for_certain_payment_method
Feature: Cancel unpaid orders for certain payment method
	In order to cancel unpaid orders which is too old for certain payment method
	As an Administrator
	I want to cancel orders that are not paid for a configured period and for certain shipping methods

	Background:
		Given the store operates on a single channel in "United States"
		And the store has "VAT" tax rate of 15% for "Tools" within the "US" zone
		And the store has a product "Screwdriver" priced at "$8.00"
		And it belongs to "Tools" tax category
		And the store has "DHL" shipping method with "$5.00" fee
		And the store allows paying with "Cash on Delivery"
		And the store also allows paying with "CSOB"

	@ui
	Scenario: The order will be canceled, older than limit and payment allowed canceling
		Given the guest customer placed order with number "00000001" with "Screwdriver" product for "john+1@snow.com" and "United States" based shipping address with "DHL" shipping method and "CSOB" payment
		And this order is "3" days old
		And I cancel orders
		And I am logged in as an administrator
		When I view the summary of the order "00000001"
		Then its state should be "Cancelled"
		And it should have payment state "Cancelled"

	@ui
	Scenario: The order wont be canceled, older than limit and payment not allowed canceling
		Given the guest customer placed order with number "00000002" with "Screwdriver" product for "john+2@snow.com" and "United States" based shipping address with "DHL" shipping method and "Cash on Delivery" payment
		And this order is "3" days old
		And I cancel orders
		And I am logged in as an administrator
		When I view the summary of the order "00000002"
		Then its state should be "New"
		And it should have payment state "New"

	@ui
	Scenario: The order wont be canceled, younger than limit and payment allowed canceling
		Given the guest customer placed order with number "00000003" with "Screwdriver" product for "john+3@snow.com" and "United States" based shipping address with "DHL" shipping method and "CSOB" payment
		And this order is "1" days old
		And I cancel orders
		And I am logged in as an administrator
		When I view the summary of the order "00000003"
		Then its state should be "New"
		And it should have payment state "New"

	@ui
	Scenario: The order wont be canceled, younger than limit and payment not allowed canceling
		Given the guest customer placed order with number "00000004" with "Screwdriver" product for "john+4@snow.com" and "United States" based shipping address with "DHL" shipping method and "Cash on Delivery" payment
		And this order is "1" days old
		And I cancel orders
		And I am logged in as an administrator
		When I view the summary of the order "00000004"
		Then its state should be "New"
		And it should have payment state "New"

	@ui
	Scenario: The order wont be canceled, already paid
		Given the guest customer placed order with number "00000005" with "Screwdriver" product for "john+5@snow.com" and "United States" based shipping address with "DHL" shipping method and "CSOB" payment
		And this order is already paid
		And this order is "3" days old
		And I cancel orders
		And I am logged in as an administrator
		When I view the summary of the order "00000005"
		Then its state should be "New"
		And it should have payment state "Completed"
