@send_order_email_to_bcc_email
Feature: Send an order email to bcc email
	In order to be able to send an copy of order email to bcc email
	As an Administrator
	I want to receive an copy of order email when customer finish order

	Background:
		Given the store operates on a single channel in "United States"
		And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
		And the store has a product "Sig Sauer P226" priced at "$499.99"
		And the store ships everywhere for free
		And the store allows paying offline
		And the channel has bcc email "sylius-bcc@example.com"

	@ui
	Scenario: Send order email to customer and to bcc email after complete checkout
		Given I have product "Sig Sauer P226" in the cart
		And I have completed addressing step with email "john@example.com" and "United States" based billing address
		And I have proceeded order with "Free" shipping method and "Offline" payment
		When I confirm my order
		Then an email with the summary of order placed by "john@example.com" should be sent to him
		And an email generated for order placed by "john@example.com" should be sent to "sylius-bcc@example.com"
