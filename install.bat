@ECHO OFF
REM https://phpunit.de/manual/current/en/installation.html
REM https://phpunit.de/
curl -L -o phpunit.phar https://phar.phpunit.de/phpunit.phar
php phpunit.phar --version

echo @php "%~dp0phpunit.phar" %* > phpunit.cmd
PAUSE
