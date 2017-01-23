# mite-gsales-importer
Import time tracking entries you made in mite to gsales

##Requirements
PHP 7.0

##Setup
For the installtion you can chose between 2 proccesses. Just follow the instructions of the [Automatic Insallation](#automatic) or [Manual Installation](#manual).

###<a name="automatic"></a>Automatic Installation
When u open the Webpage first you will see a setup formular which is asking for certain information.
It basicly takes all the Data and automaticly performs the required processes needed for the project.

Things to check before processing:

- give your folders the correct access rights (7,7,7 - you can change them back afterwards), so that php can call all required exec commands
- create the database you want to use, otherwise propel will throw exceptions
- find out your composer homepath. Php is using exec commands, so it requires the env-variable composer homepath to run correctly (for us it was in the path below the projekt path, on local machines it should be the ../user/.composer/ folder)

If you want to repeat this process just remove the /config/app.json file and call your page again.

###<a name="manual"></a>Manual Installation
create an empty app.json file in the config folder

####Deactiate automatic installation
- Create an empty file `/config/app.json`

####Log
- Create an empty file `/log/app.log`

####Propel
- Copy the `propel.yaml.dist` file and rename it to `propel.yaml`
- Adjust the database connection information
- Set the correct path for the `propel.log` file

####Apis Connection Data
- Copy the `config/apis/gsales.json.dist` file and rename it to `config/apis/gsales.json`
- Copy the `config/apis/mite.json.dist` file and rename it to `config/apis/mite.json`
- In each set your connection information

####Database
- Create an empty datanbase with the same name from the propel.yaml file

####Command Line
- `php composer.phar install` // installs all the composer packages required for the project
- `vendor/bin/propel convert-conf` // converts your settings in the propel.yaml to a config file
- `vendor/bin/propel migration:diff` // generates a migration file to prepare the changes for your database
- `vendor/bin/propel migration:migrate` // executes the changes for your database
- `vendor/bin/propel model:build` // generates the propel models
- `php composer.phar dump-autoload` // builds the autoload file, required after propels model build

