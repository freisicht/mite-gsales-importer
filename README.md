# mite-gsales-importer
Import time tracking entries you made in mite to gsales

##Requirements
PHP 7.0

##Setup

###Log
- Create an empty file `/log/app.log`


###Propel
- Copy the `propel.yaml.dist` file and rename it to `propel.yaml`
- Adjust the database connection information
- Set the correct path for the `propel.log` file

###Apis Connection Data
- Copy the `config/apis/gsales.json.dist` file and rename it to `config/apis/gsales.json`
- Copy the `config/apis/mite.json.dist` file and rename it to `config/apis/mite.json`
- In each set your connection information

###Database
- Create an empty datanbase with the same name from the propel.yaml file

###Command Line
- `php composer.phar install` // installs all the composer packages required for the project
- `vendor/bin/propel convert-conf` // converts your settings in the propel.yaml to a config file
- `vendor/bin/propel migration:diff` // generates a migration file to prepare the changes for your database
- `vendor/bin/propel migration:migrate` // executes the changes for your database
- `vendor/bin/propel model:build` // generates the propel models
- `php composer.phar dump-autoload` // builds the autoload file, required after propels model build

