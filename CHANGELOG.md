<h2>Changelog</h2>
<h3>1.3.0 - Asset management</h3>
<ul>
    <li>Added an assets page. This shows all asset types that are stored (Optic, CPUs, RAM, PSUs, Fans, HDDs)</li>
    <li>Changed the Nav links from optics to assets and all associations</li>
    <li>Trialling an API (api.php) to GET information from the database</li>
    <li>Added a CPU storage page (CPUs.php - this could change still)</li>
    <li>Database structure changes for new asset management</li>
    <li>API Keys being stored in the Database</li>
</ul>
<h3>1.2.3 - Credential features and optic importing</h3>
<ul>
    <li>Updated changepassword page to include the new credentials features from the signup page</li>
    <li>Signup page password meter adjusted to use correct colour scheme</li>
    <li>Added the optic-import.php page for adding optics from a csv</li>
    <li>Added the includes/opticimport.inc.php page to handle the optic-import logic</li>
    <li>Optic importing now possible from a csv including provided template</li>
    <li>Admin page now correctly deletes sites again for the location management section</li>
    <li>Admin page now correctly displays the hidden deleted sites with the spacing and add button hidden too</li>
    <li>Added a version checker that runs every 15 minutes when the page is refreshed. Hover the version number to see it.</li>
    <li>Added the changelog to the about page.</li>
    <li>Added pagination to the changelog for large content</li>
    <li>Added pagination to the transactions page.</li>
</ul>

<h3>1.2.2 - Auditing fixes</h3>
<ul>
    <li>Removed the nav button dimming on the favourites page.</li>
    <li>Fixed the audit.php page not working due to a missing csrf token.</li>
    <li>Added a JS scroll feature for anchor tags to head.js to stop the content being hidden under the banner.</li>
    <li>Updated all GitLab references.</li>
    <li>New README format.</li>
    <li>Changelog moved to CHANGELOG.md</li>
    <li>Fixed the install script not allowing a +30 days timestamp for bypass_2fa.</li>
    <li>Fixed the styling for the favourites error message box</li>
    <li>Fixed the caching on the stock-selectboxes.inc.php ajax requests</li>
    <li>Correctly hidden the "show deleted" button on the admin stock locations table.</li>
    <li>Added a show/hide password button on the login page.</li>
    <li>Added assets/js/passwords.js</li>
    <li>Added a signup page and the logic to turn it off in the admin settings.</li>
    <li>Added the ability to add new speeds to the optic_speed table from the optic page.</li>
    <li>Added the missing optic attributes (distance and speed) to the optic attribute managemend in the admin page.</li>
</ul>

<h3>1.2.1 - Quality of life changes</h3>
<ul>
    <li>Added FontAwesome icons to the burger menu list to clearly display what each link does</li>
    <li>Changes some css to allow the burger menu to show correctly with the new changes</li>
    <li>Added a favourites table to DB</li>
    <li>Added a favourites star icon below the banner</li>
    <li>Made the banner position:fixed to stop it moving down the page</li>
    <li>Added a function to add/remove a favourite using AJAX when the favourite star is clicked</li>
    <li>Added includes/favourites.inc.php page to do the favouriting logic</li>
    <li>Added a favourites section on the user burger menu</li>
    <li>Can remove favourites from the favourites.php page</li>
    <li>Split the stock and favourites JS into separate files</li>
    <li>Adjusted a bunch of pages to work with the new fixed banner.</li>
    <li>Favourites button is now coloured to the general banner colour.</li>
</ul>

<h3>1.2.0 - Mobile Form Factor and additional fixes</h3>
<ul>
    <li>Fixed the burger menu on the nav to show the containers page and fit on the screen correctly.</li>
    <li>Fixed the 2FA issuer being 'testing' to now getting the system name from the DB.</li>
    <li>Fixed the 2FA prompt showing weird on mobile.</li>
    <li>Changed the login_log ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.</li>
    <li>Changed the login_failure ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.</li>
    <li>Changed the session_log ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.</li>
    <li>Changed the bypass_2fa ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.</li>
    <li>Moved JS from the standard php files to the assets/js folder where possible and sourced them in.</li>
    <li>Added a "Add New" button to the manufacturer select on the stock page.</li>
    <li>Fixed the SKU patterns on the stock-*.inc.php pages.</li>
    <li>Fixed a bunch of meta tags and preloading in the head.php</li>
    <li>Hopefully finally fixed the theme_id error received when a user first logs in</li>
</ul>

<h3>1.1.2 - Bug fixes</h3>
<ul>
    <li>Fixed the stock page showing duplicate items based on manufacturer.</li>
    <li>Removed the email address prompt on the login page.</li>
</ul>

<h3>1.1.1 - 2FA Cookie fixes</h3>
<ul>
    <li>Bypass_2fa now uses randomly generated cookies and cookie names, stored in the DB</li>
    <li>Adjusted login scripts to check for the new cookies.</li>
    <li>Secret now removed from the form on the 2FA input and moved to the SESSION array.</li>
    <li>2FA functions now more secure and no hidden info left on the page.</li>
    <li>Fixed the login error output not showing correctly by adding some ajax to query the responsehandling inc page.</li>
</ul>

<h3>1.1.0 - 2FA Integration</h3>
<ul>
    <li>Added CSRF token checking to login.inc.php. This was missing.</li>
    <li>Added ids to login.php inputs</li>
    <li>Changed all of the Location headers in the login.inc.php to be returns for ajax.</li>
    <li>Added the Google Authenticator package</li>
    <li>Added the Google Authenticator url to the meta tag</li>
    <li>Added login.js for all the login js bits</li>
    <li>Added 2fa.inc.php for the 2fa bits</li>
    <li>Reconfigured the login.inc.php page to fully work via AJAX and check for 2FA settings.</li>
    <li>mysqldump of new changes added.</li>
    <li>Changes noted in the update bash file</li>
    <li>Admin page now shows larger tables better</li>
    <li>Admin page now has an authentication section for toggling 2FA globally.</li>
    <li>All swipe card code has been commented out as this is likely not going to be used. Can be removed later.</li>
    <li>Fixed the success message on the addlocaluser page</li>
    <li>Profile page now allows users to toggle 2FA except for the root user. This is actioned by admin.inc.php</li>
    <li>Profile page allows you to reset your 2FA.</li>
    <li>Admin users table now has a reset 2FA option for resetting other users' 2FA</li>
    <li>Root user can no longer have 2FA prompts.</li>
    <li>2FA prompts now submit on enter key.</li>
    <li>2FA can now be "remembered" so you only have to input it once every 30 days on a device</li>
    <li>Resetting a local user password will clear all "remembered" 2FAs for the user</li>
</ul>

<h3>1.0.1 - 1.0.1 - Login history</h3>
<ul>
    <li>Added a login history to the profile page.</li>
    <li>Fixed the stock description showing the /r/n instead of line breaks on the stock edit text area and the stock main page.</li>
    <li>Image management section of the admin page, now loads images on an interval of 20 per button click to save loading times.</li>
    <li>Added a credential login checker on the db credentials to redirect to an error page if there is an issue.</li>
</ul>

<h3>1.0.0 - Official 1.0.0 release.</h3>
<ul>
    <li>Removed all sensitive data from all versions.</li>
    <li>Fixed the profile page json stopping text input.</li>
    <li>Removed all references to affected_rows() due to deprecation.</li>
    <li>Fixed the smtp test page to allow no username and no password. Also now works with no ssl/tls.</li>
    <li>Allowed admins to save blank auth username/password to the db.</li>
    <li>Removed the system name from the subject of all email.</li>
    <li>Nav dropdown menu now opens on mouse over.</li>
    <li>Added notifications for adding, removing and moving optics.</li>
    <li>Updated the notifications table to add the new notifications.</li>
    <li>Added a type dropdown filter on the cables page.</li>
    <li>All time/date variable are now in the same format. </li>
    <li>Any cost input now has a scale to 2 decimal places.</li>
    <li>Added changelog entries for logging in with LDAP.</li>
    <li>Fixed the csrf_token being missing on some admin user areas.</li>
    <li>Fixed the logout changelog entry to actually showing logout as the reason.</li>
</ul>

<h3>0.7.2-beta - Beta release 0.7.2, CSRF Token added, optic distance added.</h3>
<ul>
    <li>Added CSRF tokens and some slight changes to some files to make it work better.</li>
    <li>Added CSP policy meta header to head.php.</li>
    <li>Removed old AJAX/jquery references in head.php.</li>
    <li>Added an Anti-clickjacking header in head.php (in php).</li>
    <li>Used htmlspecialchars() on $_GET requests that print to the page to stop injection.</li>
    <li>Fixed the get-config php page to make the theme defaults strings not an array.</li>
    <li>Fixed the changelog not showing login failures/attempts.</li>
    <li>Added optic_distance table.</li>
    <li>Added distance_id to optic_item table.</li>
    <li>Added spectrum field to optic_item table to show wavelength.</li>
    <li>Added the logic for adding distances to the DB.</li>
    <li>Fixed the optics page to show the correct info.</li>
</ul>

<h3>0.7.1-beta - Beta release 0.7.1, Some script fixes and visual changes.</h3>
<ul>
    <li>Added a checker for any MySQL servers on the system before installing mysql. Uses the existing one if exists.</li>
    <li>Adding stock properties now correctly adds shelves.</li>
    <li>Fixed the stock image editing to make the images fit in the table better with a max height added</li>
    <li>Fixed the admin page user table to look nicer and less squashed. </li>
    <li>Changed the padding on the buttons in the user table to look nicer</li>
    <li>Index page now only loads the non-deleted manufacturer/tags and in alphabetical order.</li>
    <li>Added a row count to the deleted stock under stock management in admin.php</li>
    <li>Ajax select boxes now order by name rather than id</li>
    <li>Removed the form elements from the new-properties page to stop it redirecting needlessly and breaking.</li>
    <li>Added some special character captures for the confirmAction on the stock removal page when deleting a stock object.</li>
    <li>Index manufacturer drop down now shows exact manufacturer matches instead of partial matches.</li>
    <li>Login log should now get the user id on login.</li>
    <li>Login page now encrypts the data sent on login form</li>
    <li>Login inc page no longer LDAP escapes the password. This was causing issues and was not necessary.</li>
    <li>Added csrf tokens based on an OWASP vulnerability. This is done in session.php.</li>
</ul>

<h3>0.7.0-beta - Beta release 0.7.0, Login tracking and blocking, containers and container logic.</h3>
<ul>
    <li>Added login_log table to track login attempts.</li>
    <li>Added login_failure table to track failed login count.</li>
    <li>Renamed sessionlog table to session_log.</li>
    <li>Added login_log_id to session_log table.</li>
    <li>New include file added for login tracking and blocking, as includes/login-functions.inc.php</li>
    <li>Adjusted the login.in, session.inc and logout php pages to accommodate the new login blocking and tracking.</li>
    <li>Fixed some LDAP testing bugs.</li>
    <li>"parent_id" field dropped from area table. This was unused.</li>
    <li>"is_container" field added to item table. This marks the item as a container.</li>
    <li>Containers link added to nav bar.</li>
    <li>containers.inc.php page added for the container logic.</li>
    <li>Containers can be added from the containers page.</li>
    <li>Stock add page now has asterisks marking required fields.</li>
    <li>Items can now be linked to and unlinked from containers</li>
    <li>Stock move page now shows the container the item is in. </li>
    <li>Stock move page now warns you when moving stock that is within a container.</li>
    <li>Moving stock no longer deleted the previous one and adds a new copy. No idea why I did this...</li>
    <li>Removing stock page now only shows the serials of the selected manufacturer. This was missed before and it showed all for the shelf regardless of manufacturer.</li>
    <li>Container field added to the remove stock page and checks for the container the item is in for removal.</li>
    <li>Removing a container now prompts to remove/move the contents</li>
    <li>The remove page now shows what is and is not in a container.</li>
    <li>Containers page now shows the location of the container. The SQL query for this is rather large though, so might need to be changed at a later date.</li>
    <li>Stock page buttons are now inline with the Stock heading</li>
    <li>Removed all references to "cotnainer"...</li>
    <li>Can now remove children from containers on the containers page</li>
    <li>Can now link and unlink children from the stock page</li>
    <li>Can now add children on the containers page.</li>
    <li>Can now see containers which have no children on the containers page.</li>
</ul>

<h3>0.6.0-beta - Beta release 0.6.0, Optics stocking, Auditing and database renamed to stockbase.</h3>
<ul>
    <li>Optic modules now stocked under optics.php</li>
    <li>optics.php shows the list of optics in store for each site similar to how the index page shows the main stock.</li>
    <li>Comments can be added to the optics</li>
    <li>Searching for optics searches through all fields rather than just model.</li>
    <li>New tables added: optic_item, optic_connector, optic_type, optic_speed, optic_vendor, optic_comment, optic_transaction, stock_audit</li>
    <li>Due to new tables being added, there will need to be some SQL adjustments on updates/downgrades to this version</li>
    <li>users_roles table has a new field: is_optic</li>
    <li>Stock option added to the nav bar.</li>
    <li>Nav bar now highlights based on the page you are on.</li>
    <li>Nav bar links (right) are now a elements instead of button, so that middle click works.</li>
    <li>Version number is now pinned to the bottom right of the nav bar. This currently cannot be hidden. This will be removed come version 1.0.0</li>
    <li>All logic added for the optics page. Can now add/remove optics and comments, and add vendors and types.</li>
    <li>Profile link is now named 'Profile' in the navigation. Now that there are more links, this is clear.</li>
    <li>Optic Attribute Management is now included on admin page to manage vendors, types and connectors.</li>
    <li>Changelog now works with optic tables</li>
    <li>Database now named stockbase</li>
    <li>Update script adjusted for all the changes.</li>
    <li>IndexAjax is now using a CTE table to make things faster on large datasets.</li>
    <li>Stock Add/Remove/Move pages updated with new CTE table to speed things up.</li>
    <li>Add New Stock button on the Stock Add page now fills in the name with whatever was in the search box.</li>
    <li>Pagination has been adjusted on all pages for allowing over 5 pages.</li>
    <li>Cablestock now listed in the nav bar as "cables".</li>
    <li>Item stock button removed from cablestock.</li>
    <li>Cables button removed from index.</li>
    <li>Comments button on optics is now the message icon with a number for the count inside.</li>
    <li>Show/Hide deleted optics now possible. Can also restore them.</li>
    <li>Added Dark Black theme.</li>
    <li>Admin, Profile and Logout buttons moved from nav to "username" dropdown in top right corner.</li>
    <li>Renamed indexajax.php to stockajax.php</li>
    <li>Add/Remove/Move stock pages now load the content using js and ajax - the same as the index page.</li>
    <li>Audit page added, which has a 6 month date retention on it, meaning if the last date was 6 months ago, it will show on the audit page.</li>
    <li>Pagination added to optics and cablestock pages to match the other stock pages.</li>
    <li>Added DOCTYPE to all pages that need it to remove Quirks Mode issues.</li>
    <li>Corrected the ldap-test script to actually filter based on input.</li>
    <li>Added a border to the footer using the background colour to all css files.</li>
    <li>Added LDAP injection prevention on the login page.</li>
</ul>

<h3>0.5.0-beta - Beta release 0.5.0, Session logging and management for users, changelog improvements and some formatting.</h3>
<ul>
    <li>Added sessionlog table to database.</li>
    <li>sessionlog table tracks the login/logout/timeout/expiry of user sessions to manage their login time.</li>
    <li>New file: includes/session.inc.php added. This manages the sessions with new functions.</li>
    <li>session.php manages the session.inc.php page on each web page accessed.</li>
    <li>Update script adjusted to allow the database changes.</li>
    <li>Admin page now has a "Session Management" section to kill any inactive or suspicious sessions.</li>
    <li>Admin sections moved around to be more logical</li>
    <li>Changelog page now has onclicks to show a hidden row with the table data for the record_id</li>
    <li>Some table formatting changes to the move hidden rows. These are now cantered</li>
    <li>Fixed the assign card buttons causing instant errors and not working on profile page</li>
    <li>Added changelog filters to the changelog page. This allows time frames and table/user filtering.</li>
</ul>

<h3>0.4.2-beta - Beta release 0.4.2, Update script web server checking and feedback updates.</h3>
<ul>
    <li>Install script now checks which web servers are installed and asks which to use and whether to disable the other if there are multiple.</li>
    <li>If only one web server is installed, it uses it by default. This will be apache2 if no web server was installed initially, due to PHP installing apache2.</li>
    <li>Update script updated to accommodate 0.4.0-beta and 0.4.1-beta. 0.4.1-beta and 0.4.2-beta are the same.</li>
    <li>Manufacturer can now be changed on a per item basis under the stock page.</li>
    <li>Stock row editing save button now update to 'update'</li>
    <li>Remove button added to populate the remove form and the logic to go with this in JS</li>
    <li>Stock rows are now outlined in dark when selected to make it more obvious</li>
    <li>Themes updated with the .highlight class</li>
    <li>Index table and cablestock table now updated with each row having the highlight class</li>
    <li>Tags are now removed from the stock rows on the stock page. This is related to the stock object, not the items.</li>
    <li>Tags now have an X icon on them when editing stock. This is removed when the tag is removed, along with the clickable class.</li>
    <li>Tags edit box is now larger and allows wrapping</li>
    <li>Tags on the index stock table allow wrapping to stop the table exceeding the width limits.</li>
    <li>MySQL queries now allow for single quotes and double quotes on string entries. This is also formatted correctly on SELECTs.</li>
    <li>Index page stock name is now a link instead of onclick to allow middle mouse clicks.</li>
    <li>Moving cable stock is now possible from the cablestock.php page. This will also be possible from stock.php soon.</li>
    <li>Tags page now has the correct table highlighting on selecting rows.</li>
    <li>Footer can now be disabled/enabled in the admin page under the Footer section. </li>
    <li>DB tables: config and config_default have 3 new columns.</li>
    <li>Can now Add/Remove/Move cablestock from the stock.php page. This now loads the correct info and fields.</li>
</ul>

<h3>0.4.1-beta - Beta release 0.4.1, Cost toggles and quality of life changes.</h3>
<ul>
    <li>Fixed some page redirects for the edit stock page. Now diverts you to the stock main page if all is successful, else drops you back on the edit page.</li>
    <li>Cablestock description is now optional. This is not always relevant to the item.</li>
    <li>Stock.php now has response handling built in. This means that error messages will show correctly.</li>
    <li>LDAP settings on the admin page now has the correct error checking and response handling. There are a couple of unique ones left in place.</li>
    <li>Can now disable / enable the cost for items. This is not always needed so can be toggled off under stock management in admin.php.</li>
</ul>

<h3>0.4.0-beta - Beta release 0.4.0, Label to Tag.</h3>
<ul>
    <li>Renamed the stock_label and label table to stock_tag and tag. Moving away from the term 'label' as it is not a fit name.</li>
    <li>renamed the stock_tag table column 'label_id' to 'tag_id' to match the theme.</li>
    <li>Changed all references of label to tag in the codebase. </li>
    <li>Added tags.php page to show all tags and their connections. This is not reachable without URL currently.</li>
    <li>Stock Locations in admin page now allows you to see deleted locations and restore them, similar to attributes.</li>
    <li>Adding properties is now an ajax request (e.g. adding tags, manufacturers, shelves areas and sites in the add new stock section). This means the page doesn’t refresh.</li>
    <li>Added description to the tag table for editing on the tags page.</li>
    <li>Stock edit script now separately checks for each change.</li>
    <li>Stock edit script now only removes the tags that are no longer linked.</li>
    <li>Stock edit script now only sends emails if there have been changes.</li>
    <li>Password reset modal div now works on mobile format.</li>
</ul>

<h3>0.3.2-beta - Beta release 0.3.2, Update scripts for version management and some small feature changes.</h3>
<ul>
    <li>Update script in place. Testing required for full version changing, but this will be more relevant when the database structure changes.</li>
    <li>Added Stock Management section to admin page. This allows you to recover/restore deleted stock objects instead of creating new ones.</li>
    <li>Added Attribute Management section to admin page. This allows you to delete and recover labels and manufacturers. This may extend in the future.</li>
    <li>Changelog event added to stock-new-properties.inc.php. This is for adding labels, manufacturers and locations.</li>
    <li>Added an impersonation feature for the root user only. This means the root user can become the user they select from the users list.</li>
    <li>Impersonation can be cancelled by clicking the button on the nav bar.</li>
    <li>Added new email notification for restoring deleted stock.</li>
    <li>Can now restore stock after deleting instead of re-creating the stock item again.
    <li>Added responsehandler.inc.php page to handle errors/success responses from page redirects. This now means the file only need to be included on the page and a function placed where the output should be seen.</li>
    <li>Collected all current error messages hard coded into files and moved them to the response handler page.</li>
    <li>Stock page now shows items that are deleted. A new prompt shows up warning you it is deleted.</li>
    <li>Stock buttons are disabled when the stock item is deleted=1.</li>
</ul>

<h3>0.3.1-beta - Beta release 0.3.1, Script updates, swipe card login.</h3>
<ul>
    <li>Transaction include page styling corrected under pagination form</li>
    <li>Swipe card login now working. Testing pending once card reader is obtained.</li>
    <li>Card login page is now complete and working. Test buttons in place for passes until pass reader in place.</li>
    <li>Users with no theme saved can now login. Fixed the SQL query to make a LEFT JOIN for theme.</li>
    <li>DB install extras updated in db_extras.sql.</li>
    <li>Fulldump run and saved.</li>
    <li>Adjustments made to various pages based on installation bash script.</li>
    <li>Edit images button added back in to the stock edit page.</li>
    <li>Login page is now working for the card reader, still needs a full test but now doesn’t try to login when pressing any button.</li>
    <li>MySQL scripts updated to add the needed info to the DB.</li>
    <li>Bash script updated with some more prompts and fixed the first prompt with a case instead of else if.</li>
    <li>Bash script now checks whether the base_url is correct and has some delay added in for the scripts to run.</li>
    <li>Admin global settings is now a more cleaned up table.</li>
    <li>Transactions now support cable_transaction table.</li>
    <li>Transaction include page now supports cable_transaction page.</li>
    <li>Updated cable_transaction table to now include the shelf_id. SQL queries updated.</li>
    <li>Added error checking from URLs to the pages where they are needed and adjusted the error query strings to be more useful.</li>
    <li>Admin global settings restore defaults now restores the default theme too.</li>
    <li>Fixed some of the forms not working due to some mobile CSS format things. There might be some more to find yet.</li>
    <li>Corrected the README with correct PHP modules to match the install bash script</li>
    <li>Fixed the install bash script to install the correct modules based on testing. Now installs correctly.</li>
    <li>Added the start of an update script. This will be perfected in the next minor patch ready for the final release in 0.4.0-beta</li>
</ul>

<h3>0.3.0-beta - Beta release 0.3.0, Adjustments for mobile width and card reader tech.</h3>
<ul>
    <li>Mobile CSS in progress</li>
    <li>Some HTML elements are hidden/shown based on width.</li>
    <li>Admin page is not visible from mobile form factor unless the URL is appended.</li>
    <li>New CSS added for mobile form factor.</li>
    <li>Nav now loads properly on mobile.</li>
    <li>Footer now loads differently on mobile.</li>
    <li>Index page now works on mobile. Less columns show to reduce clutter</li>
    <li>Cablestock page now works on mobile.</li>
    <li>Stock (view) page now works on mobile.</li>
    <li>Stock (add) page now works on mobile.</li>
    <li>Stock (remove) page now works on mobile.</li>
    <li>Stock (move) page now works on mobile.</li>
    <li>Stock (edit) page now works on mobile.</li>
    <li>Transactions inc now working on mobile, with page numbers becoming a select field.</li>
    <li>Index page pagination row is now longer being sorted with the rest of the table.</li>
    <li>Swipe card prompt now shows up on mobile form factor.</li>
    <li>Swipe card fields added to users table.</li>
    <li>Swipe cards can now be added on the profile page.</li>
    <li>Swipe cards can be re-assigned on the profile page.</li>
    <li>login-card.inc.php added to handle card logins.</li>
    <li>Swipe card assigning and re-assigning is handled in admin.inc.php.</li>
    <li>Swipe card de-assigning is handled in admin.inc.php.</li>
    <li>Bootstrap 4.5.2 CSS added in assets/css folder for redundancy.</li>
    <li>Email example added to Email Notification Settings section of admin page via AJAX.</li>
    <li>Some modification to the smtp.inc.php email template to allow it to be embedded in pup page.</li>
</ul>

<h3>0.2.1-beta - Beta release 0.2.1, based on initial feedback.</h3>
<ul>
    <li>Added more themes. Theme CSS now has more properties which can be adjusted.</li>
    <li>Changelog page has been formatted better and now fills the page.</li>
    <li>Email notifications can now be disabled from the admin page. Each notification type is configurable, except them important things, like password resets.</li>
    <li>SMTP send_mail function has been updated to check if the notification type is enabled. All send_mail requests have been updated accordingly.</li>
    <li>Default theme selection now added to Global Settings of admin page</li>
    <li>Themes are now in their own table in the database and theme selection is now auto generated on pages.</li>
    <li>Theme test page added ad theme-test.php. This shows all relevant pages which are affected by the theme. The css can be edited, applied, downloaded and uploaded from here for new themes.</li>
    <li>About page added, accessible from the footer copyright.</li>
    <li>The http-headers.php is now merged into head.php. These were both being called at the same time so seemed pointless being split.</li>
    <li>Name and branding changes to StockBase. This might not be the final name.</li>
    <li>Footer can now be disabled from the $showFoot variable in foot.php - this will likely hidden on final release.</li>
    <li>Emails now have useful content in them. It used to be just numbers but now it gives relevant info.</li>
    <li>MySQL dumps updated</li>
</ul>

<h3>0.2.0-beta - Beta release 0.2.0, based on initial feedback.</h3>
<ul>
    <li>Corrected the URL redirects when a user tries to reach a page without being logged in. Logging in now redirects to the correct page.</li>
    <li>Removed the title and welcome message from the index page and cablestock pages.</li>
    <li>Moved the title into the Nav bar and linked it to the index page.</li>
    <li>Corrected the issue with the offset being negative when no items are found on the index SQL query. Negative numbers now default to 0.</li>
    <li>Back button removed from the nav. This was creating loops where you couldn’t actually go back.</li>
    <li>Changed the icon in the clear button to be the fa-ban icon and rotated it 90degrees</li>
    <li>Changed the serial number so it can now be copied but this may be going later down the line</li>
    <li>Stock page now allows you to edit individual rows in the item table. This allows the adding of new serial numbers which were missed.</li>
    <li>Images can be permanently deleted from the admin page</li>
    <li>Cable stock now relates to shelves rather than just sites. This is now added correctly too.</li>
    <li>Stock page now hides irrelevant info for cables.</li>
    <li>Less important info is now under the "more info" section on the stock page.</li>
    <li>The "show 0 stock" button now ONLY shows 0 stock rows, now all rows.</li>
    <li>Corrected the cablestock searching and formatting.</li>
    <li>Dynamic searching is now in and working. Ajax based searching which updates on input.</li>
    <li>Can now search with more criteria on the home page.</li>
    <li>Images are now larger on the home page. This is copied throughout.</li>
    <li>Cablestock page now allows you to go to the stock properties page by clicking the cable name. This is the same as normal stock items, with less important info removed. </li>
    <li>Can now change the image for cablestock with the above change.</li>
    <li>Label and Manufacturer are now select boxes rather than input because this makes more sense.</li>
    <li>Added deleted field to tables (item, stock, shelf, area etc) so that things can be tracked.</li>
    <li>Deleting stock when the stock count is 0, no longer deletes the row from the database and instead marks deleted as 1.</li>
    <li>Minimum stock count now checks against the site using the shelf of the object that the stock was removed from for the email notifications.</li>
    <li>Added a light theme (for those who no longer want their eyes), which can be enabled under the user profile section.</li>
    <li>Added more themes. Theme CSS now has more properties which can be adjusted.</li>
    <li>Email notification settings section added to admin page. This is a work in progress.</li>
    <li>Changelog section added to admin page. Moved from the hidden link and now shows 10 by default, with a link to the full page</li>
</ul>

<h3>0.1.0-beta - First beta test release of the system to be tested for install and running functionality</h3>
<ul>
    <li>Fully functioning changelog reachable from the secret admin menu - this will have a home eventually.</li>
    <li>Fully functional LDAP login system with failover host integration.</li>
    <li>Fully functional SMTP mail sending. All information is saved and pulled from the DB when requested.</li>
    <li>Fully customisable global settings, including logo, system name and banner colour.</li>
    <li>Email password resetting now possible and working.</li>
    <li>Bash install script implemented and functional. May need adapting as changes happen to the system.</li>
    <li>Email template created but could probably use some work.</li>
    <li>Stock editing, adding, removing and moving all functional to limited testing. Further testing needed.</li>
</ul>