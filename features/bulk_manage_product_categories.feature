@bulk_manage_product_categories
Feature: Bulk manage product categories
    In order to efficiently manage product categories for multiple products
    As an Administrator
    I want to be able to bulk edit taxons for selected products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothing" taxonomy
        And the store has "Books" taxonomy
        And the store has "Electronics" taxonomy
        And the store has "Sports" taxonomy
        And the store has "Others" taxonomy
        And the store has a product "T-Shirt"
        And the store has a product "Novel Book"
        And the store has a product "Laptop"
        And I am logged in as an administrator

    @ui @mink:panther
    Scenario: Successfully bulk manage categories with replace action for main taxon and other taxons
        Given the product "T-Shirt" has a main taxon "Clothing"
        And the product "T-Shirt" belongs to taxon "Sports"
        And the product "Novel Book" has a main taxon "Books"
        And the product "Novel Book" belongs to taxon "Sports"
        When I browse products
        And I select the "T-Shirt" and "Novel Book" products for bulk action
        And I choose bulk action "bulk-setProductCategories"
        Then I should be on the bulk manage product categories page with selected products "T-Shirt" and "Novel Book"
        When I set main taxon to "Electronics" with "replace" action
        And I set taxons to "Electronics" and "Sports" with "replace" action
        And I save the bulk categories changes
        Then I should be notified that the categories have been successfully saved
        And I should be redirected to the product index page
        And the "T-Shirt" product should have "Electronics" as its main taxon
        And the "T-Shirt" product should belong to "Electronics" and "Sports" taxons
        And the "Novel Book" product should have "Electronics" as its main taxon
        And the "Novel Book" product should belong to "Electronics" and "Sports" taxons

    @ui @mink:panther
    Scenario: Successfully bulk manage categories with add action for main taxon and other taxons
        Given the product "T-Shirt" has no main taxon
        And the product "Novel Book" has a main taxon "Books"
        And the product "Laptop" has no main taxon
        When I browse products
        And I select the "T-Shirt", "Novel Book" and "Laptop" products for bulk action
        And I choose bulk action "bulk-setProductCategories"
        Then I should be on the bulk manage product categories page with selected products "T-Shirt", "Novel Book" and "Laptop"
        When I set main taxon to "Clothing" with "add" action
        And I set taxons to "Electronics" and "Sports" with "add" action
        And I save the bulk categories changes
        Then I should be notified that the categories have been successfully saved
        And the "T-Shirt" product should have "Clothing" as its main taxon
        And the "T-Shirt" product should belong to "Electronics" and "Sports" taxons
        And the "Novel Book" product should have "Books" as its main taxon
        And the "Novel Book" product should belong to "Electronics" and "Sports" taxons
        And the "Laptop" product should have "Clothing" as its main taxon
        And the "Laptop" product should belong to "Electronics" and "Sports" taxons

    @ui @mink:panther
    Scenario: Successfully bulk manage categories with remove action for main taxon and other taxons
        Given the product "T-Shirt" has a main taxon "Clothing"
        And this product belongs to "Sports" and "Others"
        And the product "Novel Book" has a main taxon "Books"
        And this product belongs to "Sports" and "Electronics"
        When I browse products
        And I select the "T-Shirt" and "Novel Book" products for bulk action
        And I choose bulk action "bulk-setProductCategories"
        Then I should be on the bulk manage product categories page with selected products "T-Shirt" and "Novel Book"
        When I set main taxon to "Clothing" with "remove" action
        And I set taxons to "Sports" and "Electronics" with "remove" action
        And I save the bulk categories changes
        Then I should be notified that the categories have been successfully saved
        And the "T-Shirt" product should have no main taxon
        And the "T-Shirt" product should have no taxons
        And the "Novel Book" product should have "Books" as its main taxon
        And the "Novel Book" product should have no taxons

    @ui @mink:panther
    Scenario: Successfully bulk manage categories with remove_all action
        Given the product "T-Shirt" has a main taxon "Clothing"
        And this product belongs to "Clothing" and "Sports" and "Electronics"
        And the product "Novel Book" has a main taxon "Books"
        And this product belongs to "Books" and "Sports" and "Electronics"
        When I browse products
        And I select the "T-Shirt" and "Novel Book" products for bulk action
        And I choose bulk action "bulk-setProductCategories"
        Then I should be on the bulk manage product categories page with selected products "T-Shirt" and "Novel Book"
        When I set main taxon with "remove_all" action
        And I set taxons with "remove_all" action
        And I save the bulk categories changes
        Then I should be notified that the categories have been successfully saved
        And the "T-Shirt" product should have no main taxon
        And the "T-Shirt" product should have no taxons
        And the "Novel Book" product should have no main taxon
        And the "Novel Book" product should have no taxons

    @ui @mink:panther
    Scenario: Mixed actions - add main taxon and replace other taxons
        Given the product "T-Shirt" has no main taxon
        And this product belongs to "Clothing" and "Sports"
        And the product "Novel Book" has a main taxon "Books"
        And this product belongs to "Books" and "Sports"
        When I browse products
        And I select the "T-Shirt" and "Novel Book" products for bulk action
        And I choose bulk action "bulk-setProductCategories"
        Then I should be on the bulk manage product categories page with selected products "T-Shirt" and "Novel Book"
        When I set main taxon to "Electronics" with "add" action
        And I set taxons to "Electronics" with "replace" action
        And I save the bulk categories changes
        Then I should be notified that the categories have been successfully saved
        And the "T-Shirt" product should have "Electronics" as its main taxon
        And the "T-Shirt" product should belong to "Electronics" taxon only
        And the "Novel Book" product should have "Books" as its main taxon
        And the "Novel Book" product should belong to "Electronics" taxon only
