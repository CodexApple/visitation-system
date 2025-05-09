## Setting up Server for Deployment
### Updating the system
Install the necessary updates and patches for your system environment.
```console
sudo apt-get update
sudo apt-get upgrade --yes
```

### Adding the PHP repository
```console
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get upgrade --yes
```
### Installing Nginx
Install your desired web server. In this deployment, we'll be using Nginx.
```console
sudo apt-get update
sudo apt install nginx
```

### Installing and Setting up Composer
Install the PHP Command Line prompt and an archive manager
```console
sudo apt install php-cli unzip
```

Installing composer and setting as a global executable command.
Download the composer installer from getcomposer.org
```console
cd ~ && curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
```
Verify the installer if it matches the lasted md5 hash installer
```console
HASH=`curl -sS https://composer.github.io/installer.sig`
php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```
If the installer has been verified, run the following command below to allow it to be executable globally
```console
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
```
Check if composer is now an executable command
```console
composer
```
### Getting the right PHP Version
Should you get an error in version entailing about PHP 8.3, please peform the following command
```console
sudo apt update
sudo apt install php8.3
sudo update-alternatives --install /usr/bin/php php /usr/bin/php8.3 83
sudo update-alternatives --config php
sudo apt install php8.3-cli php8.3-common php8.3-mbstring php8.3-xml php8.3-curl php8.3-mysql
```
## Setting up MySQL
Installing MySQL server, this step process you will simply have to follow what is on your screen
```console
sudo apt update
sudo apt install mysql-server
sudo systemctl start mysql.service
sudo mysql_secure_installation
```
### Error in MySQL Password setup
To recover your MySQL installation process, firstly sudo into mysql to run as root
```console
sudo mysql
```
Then alter the users password by performing the command below
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
exit;
```
Login to the root user and modify its security checks to allow root ssh without inputting the required password.
```console
mysql -u root -p
```
This will use the password system managed by your unix kernel instead of the mysql_auth
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket;
```

## Setting up Laravel
Clone the project repository to the appropriate directory and go to that directory
```console
git clone https://github.com/CodexApple/visitation-system.git pcs.localhost.test
cd /var/www/pcs.localhost.test
```
Set the appropriate folder and file permission to the project directory, run the `pwd` command to see your current directory it should be `/var/www/pcs.localhost.test`
```console
pwd

sudo chown -R $USER:www-data .
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 775 {} \;
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```
Install the necessary project files and dependencies
```console
composer install
composer update
npm install
npm run build
```
Copy the .env.example file and create a link for the storage application
```console
sudo cp .env.example .env
php artisan key:generate
php artisan storage:link
```

### Change the following Environment Variables
FROM
```dotenv
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost
```
TO
```dotenv
APP_NAME=<YOUR-APPLICATION-NAME>
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Manila
APP_URL=<YOUR-APP-URL>
```
Modify the database Environment Variables
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<YOUR-MYSQL-DATABASE>
DB_USERNAME=<YOUR-MYSQL-USERNAME>
DB_PASSWORD=<YOUR-MYSQL-PASSWORD>
```

### Additional Environment Variables
```dotenv
REVERB_APP_ID=<YOUR-RANDOM-6-DIGIT-ID>
REVERB_APP_KEY=<YOUR-RANDOM-APP-KEY>
REVERB_APP_SECRET=<YOUR-RANDOM-APP-SECRET-KEY>
REVERB_HOST=<YOUR-HOSTNAME-OR-IP-ADDRESS>
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Other Changes
Please check your **.env** file it will be missing during the installation process this is because it contains various secret information only **YOU** should know.
