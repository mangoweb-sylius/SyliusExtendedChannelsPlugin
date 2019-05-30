@send_order_email_to_bcc_email
Feature: Send an order email to bcc email
	In order to be able to send an order email to bcc email
	As a Administrator
	I want to receive an order email when customer finish order

	Background:
		Given the store operates on a single channel in "United States"
		And the channel has bcc email "sylius-bcc@mangoweb.cz"
		And the store has "VAT" tax rate of 15% for "Tools" within the "US" zone
		And the store has a product "Screwdriver" priced at "$8.00"
		And it belongs to "Tools" tax category
		And the store has "DHL" shipping method with "$5.00" fee
		And the store allows paying with "Cash on Delivery"
		And there is a customer "sylius@mangoweb.cz" that placed an order "#00000001"
		And the customer bought 10 "Screwdriver" products
		And the customer "Mango Web" addressed it to "Street", "12345" "Los Angeles" in the "United States"
		And for the billing address of "Mango Web" in the "Street", "12345" "Los Angeles", "United States"
		And the customer chose "DHL" shipping method with "Cash on Delivery" payment

	@ui
	Scenario: Send order email to customer and to bcc email after complete checkout
		Given shop send an email after finished order
		Then an email generated for order "00000001" should be sent to "sylius@mangoweb.cz"
		And an email generated for order "00000001" should be sent to "sylius-bcc@mangoweb.cz"
