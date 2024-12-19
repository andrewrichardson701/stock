<a id="readme-top"></a>

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]


<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://gitlab.com/andrewrichardson701/stockbase">
    <img src="assets/img/config/default/default-logo.png" alt="Logo" width="80" height="80">
  </a>

<h3 align="center">Stockbase</h3>

  <p align="center">
    An inventory management and asset tracking system, tailed to data centres, with less of the bloat.
    <br />
    <a href="https://gitlab.com/andrewrichardson701/stockbase"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://stockbase-demo.ajrich.co.uk">View Demo</a>
    ·
    <a href="https://gitlab.com/andrewrichardson701/stockbase/issues/new?labels=bug&template=bug-report---.md">Report Bug</a>
    ·
    <a href="https://gitlab.com/andrewrichardson701/stockbase/issues/new?labels=enhancement&template=feature-request---.md">Request Feature</a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#changelog">Changelog</a></li>
    <li><a href="#file-breakdown">File Breakdown</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://stockbase-demo.ajrich.co.uk)

Stockbase is an inventory management and asset tracking system, tailored to data centres, designed to have less of the bloat that commercial and other open source systems have.

The design aims to make managing and tracking devices, parts, cables, fibre optics and anything else of use in an easy to manage platform.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Built With
* [![PHP][PHP.net]][PHP-url]
* [![Bootstrap][Bootstrap.com]][Bootstrap-url]
 * [![JavaScript][JavaScript.com]][JavaScript-url]
* [![JQuery][JQuery.com]][JQuery-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

The installation steps are important to be followed in order.

If this is not a fresh install (e.g. a new VM or you have no existing database or web server) feel free to use the Automated Deployment script.

### Dependent Packages

- Linux

- Apache2 / Nginx (user choice, or whichever is installed)

- PHP 8.3 (v8.3.8)

- php8.3, php-8.3-cli php8.3-common, php8.3-curl, php8.3-fpm, php8.3-gd, php8.3-igbinary, php8.3-imagick, php8.3-imap, php8.3-intl, php8.3-ldap, php8.3-mbstring, php8.3-mysql, php8.3-readline, php8.3-redis, php8.3-soap, php8.3-xml, php8.3-xsl, php8.3-zip

- MySQL Server (v8.0.34) (or similar DB using mysql syntax)

- PHPMailer (v6.8.0) (Packaged at includes/PHPMailer)

- Google Authenticator (v1.0.0) (Packaged at includes/GoogleAuthenticator)

- Bootstrap (v4.5.2) (included in headers)

- Jquery (v3.5.1) (included in headers)

- Font Awesome (v6.4.0) (included in headers)

- Google Font - Poppins (included in headers)

  

*These packages are all installed as part of the install script at*  `assets/scripts/install.bash`*.*


### Installation


<details>

<summary><h3>Automated Deployment (Not recommended unless on a fresh install)</h3></summary>

  

For automated deployment, run the below command to clone the repository and run the install script:

  

`git clone https://gitlab.com/andrewrichardson701/stockbase.git && /bin/bash stockbase/assets/scripts/install.bash`

  

This will run the setup for the system and provide a username and password to login with.

  

Login to your site to continue with any further setup

  

</details>

<details>

<summary><h3>Manual Deployment</h3></summary>

For manual deployment, it requires all packages to be installed manually and the database to be configured and setup correctly.

  

Clone the repo first, and the follow the below steps.

`git clone https://gitlab.com/andrewrichardson701/stockbase.git`

  

1. Update your packages and install them if you are confident they are okay to be updated

  

`sudo apt update`

  

`sudo apt upgrade`

  

2. Install PHP 8.3 and all dependencies required

  

a. Install the PHP repository

  

```

sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y

sudo add-apt-repository ppa:ondrej/php

sudo add-apt-repository ppa:ondrej/nginx-mainline

sudo add-apt-repository ppa:ondrej/apache2

sudo apt update

```

  

b. Install the package and dependencies

```

sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-fpm php8.3-gd php8.3-igbinary php8.3-imagick php8.3-imap php8.3-intl php8.3-ldap php8.3-mbstring php8.3-mysql php8.3-readline php8.3-redis php8.3-soap php8.3-xml php8.3-xsl php8.3-zip

```

  

3. Install MySQL Server and run first setup

  

*Confirm whether or not a MySQL database is installed first, for example MariaDB. If MariaDB is installed, it WILL stop the MariaDB service to break.*

*Run: 'mysql -u root -p' to confirm if there are any mysql databases installed. If it lets you login with a password, there is one already. Skip this step if it exists.*

  

```

sudo apt install mysql-server

  

sudo mysql_secure_installation

```

*Make sure to set a root password and not leave it blank.*

  

4. Install your preferred web server (apache2 and nginx are both supported here, but this can be adapted)

  

```

sudo apt install apache2

```

or

```

sudo apt install nginx

```

  

5. Setup Database

  

- Confirm there is no database named 'stockbase'

  

```

mysql -u root -p

  

USE stockbase;

quit;

```

  

If mysql throws an error, the database doesn’t exist. This is what we want.

If it does exist, it will be overwritten.

- Run the MySQL DB setup

*we will navigate to the downloaded git repo*

  

```

cd stockbase

  

mysql -u root -p < assets/sql/db_setup.sql

```

  

- Run the extras script to fill in the required tables with the information they need.

  

*This script creates the required fields for the config and config_default tables, also setting the auto-increment values*

  

```

mysql -u root -p < assets/sql/db_extras.sql

```

- Create a user for the database to verify against

  

We will first check if a user exists under the name 'stockbaseuser'.

  

```

mysql -u root -p

SELECT User, Host FROM mysql.user WHERE User='stockbaseuser' AND Host='localhost';

```

  

If no rows are returned, we will add a new user.

If there are rows, we will either need to know the current password, or drop the user.

  

Select the relevant option:

  

<details>

<summary><h5>No user found, create new</h5></summary>

  

- Create the new user, replacing `[SECRET PASSWORD]` with your password

  

```

CREATE USER 'stockbaseuser'@'localhost' IDENTIFIED BY '[SECRET PASSWORD]';

GRANT ALL PRIVILEGES ON stockbase.* TO 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

</details>

  

<details>

<summary><h5>User exists and password known</h5></summary>

  

- Grant the user permissions.

  

```

GRANT ALL PRIVILEGES ON stockbase.* TO 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

</details>

  

<details>

<summary><h5>User exists and needs to be dropped</h5></summary>

  

- Drop the user

  

```

DROP USER 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

- Create the new user, replacing `[SECRET PASSWORD]` with your password

  

```

CREATE USER 'stockbaseuser'@'localhost' IDENTIFIED BY '[SECRET PASSWORD]';

GRANT ALL PRIVILEGES ON stockbase.* TO 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

</details>

  

- Confirm you can login and access the database

  

```

mysql -u stockbaseuser -p

USE stockbase;

SELECT * FROM config_default;

```

  

If you get data returned from this and no errors it all worked as expected.

- Update the database connection php file with your new credentials

  

Edit `includes/dbh.inc.php` and change the below to your new info:

  

```

$dBUsername = 'admin';

$dBPassword = 'admin';

```

  

e.g.

  

```

$dBUsername = 'stockbaseuser';

$dBPassword = 'SecretSpecialPassword';

```

  

- Create and update the root user password for your initial user

Select a password for your initial root user.

This will be prompted to be changed once you first log in.

  

Generate your hashed password with the below, replacing `[SECRET PASSWORD]` with your password:

  

```

php -r "echo password_hash('[SECRET PASSWORD]', PASSWORD_DEFAULT); echo\"\n\";"

```

  

Run the below to add your first user, replacing `[PASSWORD HASH]` with your hashed password from above:

  

```

mysql -u stockbaseuser -p

  

INSERT INTO stockbase.users (id, username, first_name, last_name, email, auth, role_id, enabled, password_expired, password)

VALUES (1, 'root', 'root', 'root', 'root@$hostname', 'local', 0, 1, 1, '[PASSWORD HASH]]');

UPDATE stockbase.users SET id=0 where id=1;

ALTER TABLE stockbase.users AUTO_INCREMENT = 1;

```

  

6. Decide on your web URL

  

We need a base URL for the site to be located at (e.g. stockbase.domain.com)

  

Update the config with this url, replacing `[WEB DOMAIN]` with your domain name/url:

  

```

mysql -u stockbaseuser -p

  

UPDATE config SET base_url='[WEB DOMAIN]' WHERE id=1;

quit;

```

  

7. Move your files to your web server/desired location

  

*Make sure you are already in the downloaded repo folder*

  

Replace `new/folder/location/` to the folder you want your server hosted from (e.g. /var/www/html/stockbase/) including the trailing /

  

```

sudo cp -a . /new/folder/location/

```

  

Set the permissions for your new folder location

  

```

sudo chown -R www-data:www-data /new/folder/location/

sudo chmod go-rwx /new/folder/location/

```

  

8. Web config setup

  

We first need to decide whether we will use SSL for this.

  

<details>

<summary><h5>No SSL</h5></summary>

Make a note of your file locations

  

<details>

<summary>Apache</summary>

- Run the below to create the config, replacing `[DOMAIN NAME]` and `[LOCATION]` with your domain name and folder location

  

```

web_domain='[DOMAIN NAME]'

folder_name='[LOCATION]'

  

cat > /etc/apache2/sites-available/$web_domain.conf <<EOL

<VirtualHost *:80>

ServerName $web_domain

DocumentRoot $folder_name

  

<Directory $folder_name>

Options Indexes FollowSymLinks MultiViews

AllowOverride All

Require all granted

</Directory>

</VirtualHost>

EOL

```

- Enable the site

  

```

sudo a2ensite $web_domain.conf

sudo systemctl reload apache2

```

  

</details>

  

<details>

<summary>Nginx</summary>

- Run the below to create the config, replacing `[DOMAIN NAME]` and `[LOCATION]` with your domain name and folder location

  

```

cat > /etc/nginx/sites-available/$web_domain <<EOL

server {

listen 80;

server_name $web_domain;

  

root $folder_name;

index index.php index.html;

  

location / {

try_files \$uri \$uri/ /index.php?\$query_string;

}

  

location ~ \.php$ {

include fastcgi_params;

fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

fastcgi_index index.php;

fastcgi_param SCRIPT_FILENAME $document_root\$fastcgi_script_name;

}

}

EOL

```

  

- Enable the site

  

```

sudo ln -s /etc/nginx/sites-available/$web_domain /etc/nginx/sites-enabled/

sudo systemctl reload nginx

```

</details>

  

</details>

  

<details>

<summary><h5>Using SSL</h5></summary>

<em>This assumes you have an SSL certificate and will not cover Lets Encrypt but it can be used for your cert if needed.</em>

  

Make a note of your SSL key and certificate file locations

  

<details>

<summary>Apache</summary>

- Run the below, replacing `[DOMAIN NAME]`, `[LOCATION]`, `[SSL KEY]` and `[SSL CERT]` with your domain name, folder location, ssl key location and ssl cert location.

  

```

web_domain='[DOMAIN NAME]'

folder_name='[LOCATION]'

ssl_certificate='[SSL CERT]'

ssl_key='[SSL KEY]'

  

cat > /etc/apache2/sites-available/$web_domain.conf <<EOL

<VirtualHost *:80>

ServerName $web_domain

DocumentRoot $folder_name

  

<Directory $folder_name>

Options Indexes FollowSymLinks MultiViews

AllowOverride All

Require all granted

</Directory>

  

Redirect permanent / https://$web_domain/

</VirtualHost>

  

<VirtualHost *:443>

ServerName $web_domain

DocumentRoot $folder_name

  

<Directory $folder_name>

Options Indexes FollowSymLinks MultiViews

AllowOverride All

Require all granted

</Directory>

  

SSLEngine on

SSLCertificateFile $ssl_certificate

SSLCertificateKeyFile $ssl_key

  

Redirect permanent / https://$web_domain/

</VirtualHost>

EOL

```

  

- Enable to appropriate modules and enable the site

  

```

sudo enable_ssl_apache

sudo a2enmod ssl

sudo a2enmod rewrite

sudo systemctl restart apache2

sudo a2ensite $web_domain.conf

sudo systemctl reload apache2

```

  

</details>

  

<details>

<summary>Nginx</summary>

- Run the below, replacing `[DOMAIN NAME]`, `[LOCATION]`, `[SSL KEY]` and `[SSL CERT]` with your domain name, folder location, ssl key location and ssl cert location.

  

```

web_domain='[DOMAIN NAME]'

folder_name='[LOCATION]'

ssl_certificate='[SSL CERT]'

ssl_key='[SSL KEY]'

  

cat > /etc/nginx/sites-available/$web_domain <<EOL

server {

listen 80;

server_name $web_domain;

  

root $folder_name;

index index.php index.html;

  

location / {

try_files \$uri \$uri/ /index.php?\$query_string;

}

  

location ~ \.php$ {

include fastcgi_params;

fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

fastcgi_index index.php;

fastcgi_param SCRIPT_FILENAME $document_root\$fastcgi_script_name;

}

  

return 301 https://$web_domain\$request_uri;

}

  

server {

listen 443 ssl;

server_name $web_domain;

  

root $folder_name;

index index.php index.html;

  

ssl_certificate $ssl_certificate;

ssl_certificate_key $ssl_key;

  

add_header X-Frame-Options "SAMEORIGIN";

add_header X-Content-Type-Options "nosniff";

  

charset utf-8;

  

location / {

try_files \$uri \$uri/ /index.php?\$query_string;

}

  

location = /favicon.ico { access_log off; log_not_found off; }

location = /robots.txt { access_log off; log_not_found off; }

  

error_page 404 /index.php;

  

location ~ \.php$ {

fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;

include fastcgi_params;

fastcgi_read_timeout 300s;

proxy_read_timeout 600s;

fastcgi_buffers 16 16k;

fastcgi_buffer_size 32k;

}

}

EOL

```

  

- Add a symlink for this file

  

```

sudo enable_ssl_nginx

sudo ln -s /etc/nginx/sites-available/$web_domain /etc/nginx/sites-enabled/

sudo systemctl reload nginx

```

  

</details>

</details>

  

9. Login to your site to continue with any further setup

Login to your newly setup site by connecting to the domain name in your browser

  

e.g. https://stockbase.domain.local/

  

You will need to select "local" as your login type if the local toggle is shown on the login page

LDAP will be enabled by default with a config in place, which will not work on your system.

  

Login with the username 'root' and password created in step 5 (NOT the hashed password).

  

You will be prompted to make your first Site / Area / Shelf for the system, so please add one. (these can be changed later)

  

Head to the 'Admin' page from the navigation bar and configure your setup.

  

</details>

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- ROADMAP -->
## Roadmap

[Restyaboard Roadmap](https://todo.ajrich.co.uk)

### In Progress

- [ ] Add an API to GET information
- [ ] Add API key storage to the database
- [ ] Add a CPU storage page

### Planned Changes

- [ ] Add a drive storage page
- [ ] Add a RAM / Memory storage page
- [ ] Add option to link optics to site / area / shelf, not only the site
- [ ] Cable stock auditing

### Completed

- [x] Favourites list
- [x] Make the Optics search do a broader search, including spectrum
- [x] Add a "show password" button on the login screen
- [x] Add an optional 'sign up' page that can be enabled in the admin page
- [x] Enable the addition of more optic speeds.
- [x] Allow the editing of all optic attributes in the admin section.
- [x] Make a way of importing a spreadsheet, including a template sheet (csv) for optics and normal stock.
- [x] Format the output of the spreadsheet import for optics.
- [x] Add a version checker, prompting when out of date and by how many releases
- [x] Put the changelog on the about page
- [x] Add an Assets page, for links to other asset management pages (e.g. optics, drives)
- [x] Change the optics link to be assets instead on the nav
- [x] Add pagination to the changelog.php page
- [x] Add pagination to the transactions.php page

See the [open issues](https://gitlab.com/andrewrichardson701/stockbase/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- Changelog -->
## Changelog

All changes are listed in the Changelog file: [CHANGELOG.md](CHANGELOG.md)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<details>
<summary><h2>File Breakdown</h2></summary>

<details>

<summary><h3>assets/</h3></summary>

  

<details>

<summary><h3>css/</h3></summary>

  

<details>

<summary><h3>4.5.2-bootstrap.min.css</h3></summary>

  

- Bootstrap 4.5.2 css file

  

</details>

<details>

<summary><h3>carousel.css</h3></summary>

  

- Carousel css for the image carousel on stock page

  

</details>

<details>

<summary><h3>inv.css</h3></summary>

  

- Stock system specific css file.

  

</details>

<details>

<summary><h3>main.css</h3></summary>

  

- Main css file for the system, with the building blocks.

  

</details>

<details>

<summary><h3>theme-*.css</h3></summary>

  

- Theme files for the stock system to overwrite the default css.

  

</details>

  

</details>

<details>

<summary><h3>img/</h3></summary>

  

<details>

<summary><h3>config/</h3></summary>

  

- Any config images e.g. favicon/logo.

  

<details>

<summary><h3>default/</h3></summary>

  

<details>

<summary><h3>default-favicon.png</h3></summary>

  

- Default favicon.

  

</details>

<details>

<summary><h3>default-logo.png</h3></summary>

  

- Default logo.

  

</details>

  

</details>

  

</details>

<details>

<summary><h3>stock/</h3></summary>

  

- Stock image files, named based on the stock id and time/date of upload.

- There are some default images here too for cables.

  

</details>

<details>

<summary><h3>db relationships.png</h3></summary>

  

- Visual view of the database relationships.

  

</details>

</details>

<details>

<summary><h3>js/</h3></summary>

  

<details>

<summary><h3>admin.js</h3></summary>

  

- Specific JS for the admin.php page.

  

</details>

<details>

<summary><h3>audit.js</h3></summary>

  

- Specific JS for the audit.php page.

  

</details>

<details>

<summary><h3>cablestock.js</h3></summary>

  

- Specific JS for the cablestock.php page.

  

</details>

<details>

<summary><h3>carousel.js</h3></summary>

  

- Carousel specific js for images on stock page.

  

</details>

<details>

<summary><h3>changelog.js</h3></summary>

  

- Specific JS for the changelog.php page.

  

</details>

<details>

<summary><h3>credentials.js</h3></summary>

  

- JS for password manipulation

  

</details>

<details>

<summary><h3>favourites.js</h3></summary>

  

- Specific JS for the favourites.php page and stock.php favourite button.

  

</details>

<details>

<summary><h3>foot.js</h3></summary>

  

- Specific JS for the foot.php page.

  

</details>

<details>

<summary><h3>head.js</h3></summary>

  

- Specific JS for the head.php page.

  

</details>

<details>

<summary><h3>index.js</h3></summary>

  

- Specific JS for the index.php page.

  

</details>

<details>

<summary><h3>login.js</h3></summary>

  

- JS for the login page to do the 2FA and login without page navigation.

  

</details>

<details>

<summary><h3>nav.js</h3></summary>

  

- Specific JS for the nav.php page.

  

</details>

<details>

<summary><h3>optics.js</h3></summary>

  

- Specific JS for the optics.php page.

  

</details>

<details>

<summary><h3>profile.js</h3></summary>

  

- Specific JS for the profile.php page.

  

</details>

<details>

<summary><h3>signup.js</h3></summary>

  

- JS for signing up a new user. This is mostly empty.

  

</details>

<details>

<summary><h3>stock.js</h3></summary>

  

- Specific JS for the stock.php page.

  

</details>

<details>

<summary><h3>tags.js</h3></summary>

  

- Specific JS for the tags.php page.

  

</details>

<details>

<summary><h3>theme-test.js</h3></summary>

  

- Specific JS for the theme-test.php page.

  

</details>

  

</details>

<details>

<summary><h3>scripts/</h3></summary>

  

<details>

<summary><h3>install.bash</h3></summary>

  

- Install script to run through all of the install steps

  

</details>

<details>

<summary><h3>mysql-update-adjustment.bash</h3></summary>

  

- Update the mysql schema when running the update script.

  

</details>

<details>

<summary><h3>update.bash</h3></summary>

  

- Used for updating the system.

  

</details>

  

</details>

<details>

<summary><h3>sql/</h3></summary>

  

<details>

<summary><h3>db_extras.sql</h3></summary>

  

- Extra sql bits used after the db_setup.sql to setup the initial required information

  

</details>

<details>

<summary><h3>db_setup.sql</h3></summary>

  

- Database setup to create the tables.

  

</details>

<details>

<summary><h3>Stock.accdb</h3></summary>

  

- Microsoft Access file for the stockbase SQL schema and relationships.

  

</details>

  

</details>

  

</details>

<details>

<summary><h3>includes/</h3></summary>

  

<details>

<summary><h3>GoogleAuthenticator/</h3></summary>

  

- Google Authenticator package for 2FA

  

</details>

<details>

<summary><h3>PHPMailer/</h3></summary>

  

- PHPMailer package for SMTP setup.

  

</details>

<details>

<summary><h3>.errorlog_report.php</h3></summary>

  

- Send the error log to the specified email address.

- Add cronjob entry for this e.g. "55 23 * * * /usr/bin/php /var/www/stockbase/includes/.errorlog_report.php"

  

</details>

<details>

<summary><h3>2fa.inc.php</h3></summary>

  

- Creates the 2FA code and authenticates the 2FA code.

- Saves the secret to the database.

  

</details>

<details>

<summary><h3>addlocaluser.inc.php</h3></summary>

  

- Backend for the addlocaluser.php page

- Used to add local user information to the user table for login.

  

</details>

<details>

<summary><h3>admin.inc.php</h3></summary>

  

- Backend for the admin.php page and a few others with similar functions

- TBC

  

</details>

<details>

<summary><h3>audit.inc.php</h3></summary>

  

- Backend for the audit.php page

- Used to store the audit information to the database.

  

</details>

<details>

<summary><h3>cablestock.inc.php</h3></summary>

  

- Backend for the cablestock.php page

- Used for database manipulation for all cablestock changes

  

</details>

<details>

<summary><h3>change-theme.inc.php</h3></summary>

  

- Called when changing themes to update the user table with the new theme.

  

</details>

<details>

<summary><h3>changelog.inc.php</h3></summary>

  

- Included in pages where the changelog needs updates.

- Home of the changelog functions.

  

</details>

<details>

<summary><h3>changepassword.inc.php</h3></summary>

  

- Backend for the changepassword.php page

- Backend for the reset-password.php page

- Does the updating of passwords for local users in the user table.

  

</details>

<details>

<summary><h3>containers.inc.php</h3></summary>

  

- Backend for all container adjustments.

- Does the logic for changing the container database information.

  

</details>

<details>

<summary><h3>credentials.inc.php</h3></summary>


- Does the ajax post requests for the credentials verifications. 

- Used in the signup.php credentials checks.
  

</details>

<details>

<summary><h3>dbh.inc.php</h3></summary>

  

- Database credentials

- Navigates to error.php if unable to reach database.

  

</details>

<details>

<summary><h3>favourites.inc.php</h3></summary>

  

- Backend DB management for the favourites.php page to add and remove favourites.

- Used in the AJAX request in favourite.js.

  

</details>

<details>

<summary><h3>get-config.inc.php</h3></summary>

  

- Retrieves all config from the config table

- Retrieves all config from the config-default table

- Collates the 2x configs to get the actively running configuration

- Include this file to get the config

  

</details>

<details>

<summary><h3>ldap-resync.inc.php</h3></summary>

  

- Backend for re-syncing the LDAP information for the user profile.

  

</details>

<details>

<summary><h3>ldap-test.inc.php</h3></summary>

  

- Backend for testing LDAP connection on the admin page.

  

</details>

<details>

<summary><h3>login-card.inc.php</h3></summary>

  

- Backend for logging in with access passes

- This is no longer in use and will be removed in a future update.

  

</details>

<details>

<summary><h3>login-functions.inc.php</h3></summary>

  

- Home of the login and login management functions

- Queries to see if you are allowed to login or if you are blocked for failures

  

</details>

<details>

<summary><h3>login.inc.php</h3></summary>

  

- Backend for the login.php page

- Handles the logging in and confirmation of user credentials

- Handles the 2FA checking.

- Handles the LDAP connection for logins.

  

</details>

<details>

<summary><h3>optics.inc.php</h3></summary>

  

- Backend for the optics.php page

- Handles all logic for the optics

  

</details>

<details>

<summary><h3>responsehandling.inc.php</h3></summary>

  

- include this file to display errors or responses from the query string correctly on the page

- Has a collection of pre-defined response codes to translate.

  

</details>

<details>

<summary><h3>session.inc.php</h3></summary>

  

- Functions for the session.php page

- Used for storing the session in the database and querying the session

  

</details>

<details>

<summary><h3>signup.inc.php</h3></summary>


- Backend for the signup.php page

- Handles the verification and addition of credentials for new users.
  

</details>


<details>

<summary><h3>smtp-test.inc.php</h3></summary>

  

- Used for testing the SMTP configuration on the admin page

  

</details>

<details>

<summary><h3>stock-add.inc.php</h3></summary>

  

- Included on the stock page when adding stock to show the correct information

- Split off the stock.php page to reduce file size

- When ?modify=add is set, includes this page.

  

</details>

<details>

<summary><h3>stock-edit.inc.php</h3></summary>

  

- Included on the stock page when editing stock to shwo the correct information

- Split off the stock.php page to reduce file size

- When ?modify=edit is set, includes this page.

  

</details>

<details>

<summary><h3>stock-remove.inc.php</h3></summary>

  

- Included on the stock page when removing stock to show the correct information

- Split off the stock.php page to reduce file size

- When ?modify=remove is set, includes this page.

  

</details>

<details>

<summary><h3>stock-move.inc.php</h3></summary>

  

- Included on the stock page when moving stock to show the correct information

- Split off the stock.php page to reduce file size

- When ?modify=move is set, includes this page.

  

</details>

<details>

<summary><h3>stock-modify.inc.php</h3></summary>

  

- Backend for anu stock management e.g. adding/removing/moving/editing stock

- Does all the database changes for stock manipulation.

  

</details>

<details>

<summary><h3>stock-selectboxes.inc.php</h3></summary>

  

- Handles AJAX request for dynamically updated select boxes

  

</details>

<details>

<summary><h3>stockajax.inc.php</h3></summary>

  

- Handles AJAX requests for loading the stock onto the index page

- Handles AJAX requests for loading the stock onto the audit page

- Handles AJAX requests for loading the stock onto the containers page

  

</details>

<details>

<summary><h3>transactions.inc.php</h3></summary>

  

- Include this file to show the transactions at the bottom of the stock page.

- Shows the most recent transactions

  

</details>

  

</details>

<details>

<summary><h3>about.php</h3></summary>

  

- Shows version number

- Shows information about the system.

- Shows the GNU licence.

- Links to GitLab.

  

</details>

<details>

<summary><h3>addlocaluser.php</h3></summary>

  

- Used for adding a local user.

- Requires: username, password, first name, last name, email, role.

  

</details>

<details>

<summary><h3>admin.php</h3></summary>

  

- Global settings

- Change system name

- Change banner colour

- Change banner logo

- Change favicon images

- Change currency

- Change SKU Prefix

- Change Base URL

- Change Default Theme

- Restore default for all above

- Footer

- Enable/Disable Footer

- Enable/Disable Gitlab link

- Enable/Disable road map link

- Users

- Manage all users

- Change user roles

- Enable/disabled users

- Reset user passwords

- Reset 2FA secret

- Impersonate users (if root user)

- Add new local users

- User Roles

- View user role permissions

- Authentication

- Enable 2FA

- Enforce 2FA globally for every user

- Session Management

- Kill any active sessions to the site

- View active sessions

- Image management

- Load all used images

- Delete unused images

- Show image linking

- Attribute Management

- Delete/restore unused tags

- Delete/restore unused manufacturers

- Show all links for tags

- Show all links for manufacturers

- View all tags and their associations via tags.php

- Optic Attribute Management

- Delete/restore unused vendors

- Delete/restore unused types

- Delete/restore unused connectors

- Show all links for vendors

- Show all links for types

- Show all links for connectors

- Stock Management

- Enable/Disabled Costs for normal and cable stock

- Restore Deleted stock

- Stock Location Settings

- View/Add/Edit/Restore/Delete Locations

- LDAP Settings

- Enable/Disable LDAP

- View and edit LDAP settings

- Test LDAP settings

- SMTP Settings

- Enable/Disable SMTP

- View and edit SMTP Settings

- Test SMTP settings

- Email Notification Settings

- Configure notifications for emails

- Changelog

- View changelog

- Link to full changelog

  

</details>

<details>

<summary><h3>audit.php</h3></summary>

  

- Lists all items to be audited

- Saves information input in the table based on auditing

- Used to make sure the stock is still correct on the system

  

</details>

<details>

<summary><h3>cablestock.php</h3></summary>

  

- Shows all cablestock categorised by type (copper/fibre/power/other)

- Add/remove/moved cable stock

- Clicking the name takes you to the stock page for the item

- Clicking the site link sets the site filter

- Search by name

- Filter by site

- Filter by type

- Show/hide out of stock items

  

</details>

<details>

<summary><h3>changelog.php</h3></summary>
  

- View all logs

- Filter logs based on date/table/user

  

</details>

<details>

<summary><h3>changepassword.php</h3></summary>

  

- Used for changing a local user password

- If an LDAP user navigates here, it redirects back to profile

  

</details>

<details>

<summary><h3>containers.php</h3></summary>

  

- Lists containers

- Add/Remove containers

- Add/Remove items from containers

  

</details>

<details>

<summary><h3>COPYING.txt</h3></summary>

  

- GNU GENERAL PUBLIC LICENSE

  

</details>

<details>

<summary><h3>error.php</h3></summary>

  

- General error page for things like 404s

  

</details>

<details>

<summary><h3>favourites.php</h3></summary>

  

- Shows a list of the user's favourited stock.

- Favourites can also be removed here.

  

</details>

<details>

<summary><h3>foot.php</h3></summary>

  

- Footer for the website

- Shows the gitlab/version number/roadmap/copyright

  

</details>

<details>

<summary><h3>head.php</h3></summary>

  

- All required setup for every page

- Includes the fonts used

- Includes any scripts needed

- Includes the includes/get-config.inc.php page to gather the config information

- Sets the version number

- Sets the security policy

- Includes the stylesheets

- Includes the ajax script

- Includes a series of js functions

- Includes some css from the config

  

</details>

<details>

<summary><h3>index.php</h3></summary>

  

- Show all stock

- Filter stock by name, SKU, shelf, tag, manufacturer

- Filter stock by site / Area

- Show or hide out of stock

- Navigate to a stock item

- Clicking images makes them larger

- Clicking the name of a stock row navigates to the stock page

- Clicking the site sets the site filter

- Clicking the tag sets the tag filter

- Clicking the yellow clear icon clears the filters

  

</details>

<details>

<summary><h3>login.php</h3></summary>

  

- Login to user account

- Reset password if local user

- Select local/ldap user if enabled

- Prompts for 2FA if enabled

- Prompts for 2FA setup if enabled

  

</details>

<details>

<summary><h3>logout.php</h3></summary>

  

- Kills sessions

- Logs user out

- Redirects to login page

  

</details>

<details>

<summary><h3>nav.php</h3></summary>

  

- Sets up the global nav at the top of each page

  

</details>

<details>

<summary><h3>profile.php</h3></summary>

  

- Change password on local user

- Reset 2FA secret

- Enable 2FA if enabled globally

- Change theme

- Re-sync LDAP information

- View user information

- View login history

- Link to Theme Testing page

  

</details>

<details>

<summary><h3>reset-password.php</h3></summary>

  

- Reset local user password after a forced change from an admin.

- Will be redirected here if set in the user table

  

</details>

<details>

<summary><h3>session.php</h3></summary>

  

- Sets up the session for the user

  

</details>

<summary><h3>signup.php</h3></summary>

  

- Allows a new user creation without login

- Can be disabled in admin settings

- Verifies all information before submit

  

</details>

<details>

<summary><h3>stock.php</h3></summary>

  

- Shows stock info for the item

- Shows the items linked to the stock

- Shows transaction for the stock

- View full transaction log using show all link

- Allows editing the info and images

- Allows adding more

- Allows deleting stock

- Allows moving stock

- Allows adding new stock objects

- Filter by Site / Search / Type / Speed / Mode / Connector / Distance

- View/Add comments on each optic

- Add new Speed / Connector / Distance / Vendor / Type

- Fixed the admin.inc.php for fetching images. The form now deletes correctly.

  

</details>

<details>

<summary><h3>tags.php</h3></summary>

  

- View all tags and their associations

- Edit tag info

  

</details>

<details>

<summary><h3>theme-test.php</h3></summary>

  

- Shows snippets of all theme based css

- Test different themes to see what they look like

- Create new themes live

- Download theme

- Upload theme

  

</details>

<details>

<summary><h3>transactions.php</h3></summary>

  

- Shows full list of transactions for the item selected

  

</details>

</details>

</details>

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Top contributors:

<a href="https://gitlab.com/andrewrichardson701/stockbase/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=andrewrichardson701/Stockbase" alt="contrib.rocks image" />
</a>



<!-- LICENSE -->
## License

Distributed under the GNU General Public License v3.0. See `COPYING.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Project Link: [https://gitlab.com/andrewrichardson701/stockbase](https://gitlab.com/andrewrichardson701/stockbase)

<p align="right">(<a href="#readme-top">back to top</a>)</p>





<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/andrewrichardson701/Stockbase.svg?style=for-the-badge
[contributors-url]: https://gitlab.com/andrewrichardson701/stockbase/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/andrewrichardson701/Stockbase.svg?style=for-the-badge
[forks-url]: https://gitlab.com/andrewrichardson701/stockbase/network/members
[stars-shield]: https://img.shields.io/github/stars/andrewrichardson701/Stockbase.svg?style=for-the-badge
[stars-url]: https://gitlab.com/andrewrichardson701/stockbase/stargazers
[issues-shield]: https://img.shields.io/github/issues/andrewrichardson701/Stockbase.svg?style=for-the-badge
[issues-url]: https://gitlab.com/andrewrichardson701/stockbase/issues
[license-shield]: https://img.shields.io/github/license/andrewrichardson701/Stockbase.svg?style=for-the-badge
[license-url]: https://gitlab.com/andrewrichardson701/stockbase/blob/master/COPYING.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/andrewrichardson701
[product-screenshot]: assets/img/index-screenshot.png
[PHP.net]: https://img.shields.io/badge/PHP-4F5B93?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com 
[JavaScript.com]: https://img.shields.io/badge/JS-70DB4F?style=for-the-badge&logo=javascript&logoColor=white
[JavaScript-url]: https://www.javascript.com/
