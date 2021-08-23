# Devon Assignment

## Steps to Install
- Clone this repo to your local.
- Run `composer install` on the project base folder.
- Update the .env file with the database connect details.
- Run `php artisan migrate` to migrate the table migrations.
- Run `php artisan db:seed` to seed the create the admin user.
- Login using the below credentials
- Admin User
Email : admin@devon.com
Password : Password@123
- Normal user
Email : user@devon.com
Password : PasswordUser@123
- Route Details :
- Login API - https://devon-assignment.herokuapp.com/api/v1/login
- Teams List - https://devon-assignment.herokuapp.com/api/v1/teams
- Team Player List - https://devon-assignment.herokuapp.com/api/v1/teams/{Id}
- Player Details - https://devon-assignment.herokuapp.com/api/v1/players/{Id}
