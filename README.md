# PGDB
##Plavatos Game Database
_A working PHP/JS/MySQL site used to keep track of games_

###Features

 * Secure user password storage (stored as SHA256 and salted)
 * Download cover-art for games by clicking on their title (in the "All Games" and "Missing" lists only for now). Uses [TheGamesDB] (http://thegamesdb.net).
 * Multi-User support with ability to disable registration

###Requirements

Uses Bootstrap (included) for ease of design

Requires [MYSQLND] (http://php.net/manual/en/mysqlnd.install.php) (MySQL Native Driver) Please check your phpinfo to ensure it's installed or download via package manager.

###Installation

1. Pull the repo down to your www folder.
2. Import MakeDB.sql
3. Verify values exist in 'globals', 'distromethod', and 'system'.
4. Open PGDB in your browser, create first user
5. In phpMyAdmin, change your admin user's Role to 1
6. Log-in (reauthenticate)
7. Visit settings to ensure there are no DB updates, disable registration if desired
8. Add distros (Steam, Retail, PlayStation Store, etc) and systems (PS4, PC, 3DS, etc).

###Planned Enhancements

  * [MED] Implement search function
  * [HIGH] Complete Settings page
    * Complete Adding Distribution methods
    * Add Systems management
    * Ability to disable coverart downloading or make it a user-role
    * User management for admins to change roles
  * [MED] Ability to remove/rename user entries and titles
  * [MED] Batch update covers for all titles and auto-resize to prevent large files consuming bandwidth
