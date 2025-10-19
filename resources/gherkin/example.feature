Feature: User Login
  As a registered user
  I want to log into the application
  So that I can access my personalized dashboard

  Background:
    Given the user database contains the following users:
      | username | password | status |
      | john_doe | Pass123! | active |
      | jane_smith | SecurePass456 | active |
      | bob_jones | Test789 | locked |

  Scenario: Successful login with valid credentials
    Given I am on the login page
    When I enter "john_doe" as username
    And I enter "Pass123!" as password
    And I click the "Login" button
    Then I should be redirected to the dashboard
    And I should see a welcome message "Welcome, John Doe!"

  Scenario: Failed login with invalid password
    Given I am on the login page
    When I enter "john_doe" as username
    And I enter "WrongPassword" as password
    And I click the "Login" button
    Then I should remain on the login page
    And I should see an error message "Invalid username or password"
    And the password field should be cleared

  Scenario Outline: Login attempts with various invalid credentials
    Given I am on the login page
    When I enter "<username>" as username
    And I enter "<password>" as password
    And I click the "Login" button
    Then I should see an error message "<error_message>"

    Examples:
      | username   | password    | error_message                  |
      | invalid    | Pass123!    | Invalid username or password   |
      | john_doe   |             | Password is required           |
      |            | Pass123!    | Username is required           |
      | bob_jones  | Test789     | Account is locked              |

  @security @rate_limiting
  Scenario: Account lockout after multiple failed attempts
    Given I am on the login page
    When I attempt to login with incorrect credentials 5 times
    Then the account should be temporarily locked
    And I should see an error message "Too many failed attempts. Account locked for 15 minutes"

  @javascript
  Scenario: Remember me functionality
    Given I am on the login page
    When I enter valid credentials
    And I check the "Remember me" checkbox
    And I click the "Login" button
    And I close the browser
    And I reopen the browser
    Then I should still be logged in