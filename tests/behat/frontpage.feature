@theme @theme_roshnilite
Feature: Roshni Lite site home and navigation
  In order to find my way around the site
  As a user
  I need the Roshni Lite site home and the standard Moodle navigation

  Background:
    # Moodle 5.3+ installs with the site home off and login forced.
    Given the following config values are set as admin:
      | theme         | roshnilite |
      | enablemyhome  | 1          |
      | forcelogin    | 0          |
    And the following "categories" exist:
      | name    | category | idnumber |
      | Science | 0        | SCI      |
      | Arts    | 0        | ART      |
      | Empty   | 0        | EMP      |
    And the following "courses" exist:
      | fullname        | shortname | category |
      | Roshni course 1 | RC1       | SCI      |
      | Roshni course 2 | RC2       | ART      |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | One      |

  Scenario: Visitors see real courses, browse them by subject and can search them
    When I am on site homepage
    Then I should see "Roshni course 1" in the ".rl-preview" "css_element"
    And I should see "Science" in the ".rl-tabs" "css_element"
    And I should see "Arts" in the ".rl-tabs" "css_element"
    And I should not see "Empty" in the ".rl-catalogue" "css_element"
    And I should see "Roshni course 2" in the ".rl-catalogue" "css_element"
    And I set the field "Search courses" to "Roshni"
    And I press "Search"
    And I should see "Roshni course 1"
    And ".rl-sitefoot" "css_element" should exist

  Scenario: Visitors learn how the site works and find answers to common questions
    When I am on site homepage
    Then I should see "How it works" in the ".rl-steps-section" "css_element"
    And I should see "How do I join a course?" in the ".rl-faq" "css_element"
    And ".rl-testimonials-section" "css_element" should not exist
    And the following config values are set as admin:
      | testimonialquote1 | It changed how I study. | theme_roshnilite |
      | testimonialname1  | Priya Nair              | theme_roshnilite |
    And I am on site homepage
    And I should see "It changed how I study." in the ".rl-testimonials-section" "css_element"

  Scenario: Logged in users get the primary navigation on site home and inner pages
    Given I log in as "student1"
    When I am on site homepage
    Then I should see "Dashboard" in the ".primary-navigation" "css_element"
    And I follow "Dashboard"
    And I should see "Good" in the ".rl-dash__greeting" "css_element"
    And I should see "Enrolled courses" in the ".rl-dash__stats" "css_element"
    And I visit "/message/index.php"
    And ".rl-dash" "css_element" should not exist
    And I should see "Calendar" in the ".rl-sitefoot" "css_element"
