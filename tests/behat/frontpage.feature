@theme @theme_roshnilite
Feature: Roshni Lite site home and navigation
  In order to find my way around the site
  As a user
  I need the Roshni Lite site home and the standard Moodle navigation

  Background:
    Given the following config values are set as admin:
      | theme | roshnilite |
    And the following "categories" exist:
      | name    | category | idnumber |
      | Science | 0        | SCI      |
    And the following "courses" exist:
      | fullname        | shortname | category |
      | Roshni course 1 | RC1       | SCI      |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | One      |

  Scenario: Visitors see the site home sections and the quick login form
    When I am on site homepage
    Then I should see "Roshni course 1" in the ".course-section" "css_element"
    And I should see "Science" in the ".our-category" "css_element"
    And "username" "field" should exist in the ".loginnavbar" "css_element"

  Scenario: Logged in users get the primary navigation on site home and inner pages
    Given I log in as "student1"
    When I am on site homepage
    Then I should see "Dashboard" in the ".primary-navigation" "css_element"
    And ".loginnavbar" "css_element" should not exist
    And I follow "Dashboard"
    And I should see "My courses" in the ".primary-navigation" "css_element"
