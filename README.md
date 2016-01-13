# PGDB
##Plavatos Game Database
_A working PHP/JS/MySQL site used to keep track of games_

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
  * [MED] Ability to remove user entries and titles
  * [LOW] Feature to upload images (for cover art and distribution methods)
  * [LOW] Have a better display for statistics (games finished, in your list, etc) and fix glyphs/images
  
  ~~* [LOW] Add multi-user support~~
  ~~* [HIGH] Salt user passwords~~