[mite-url]: https://mite.yo.lk/
[gsales-url]: https://www.gsales.de/

# Mite -> gSales Importer

Import your tracked time from your [Mite][mite-url]-projects to the corresponding [gSales][gsales-url]-projects with ease.

(( SCREENSHOT VOM USERINTERFACE ))

## Background

It's already possible to import tracked times from Mite to gSales, but after some time we noticed that we needed a better solution. Using the existing method is complicated and we needed additional features which required a new interface.

Essentially, we wanted to create Invoices for Customers who have tracked time-entries in the latest month. Some projects should be in a separate Invoice, some could get summarized. And some time tracking entries would get skipped because there already is an 

## Requirements

A working Apache-Server with PHP 7 and MySQL.

## Setup

You can choose between two installation processes — automatic (recommended) or manual.

The automatic installation uses exec to build the required configuration files to keep the system running — it may not work on Windows Server environments. In that case, you would have to the manual method.

For both methods, you need to clone

```
git clone https://github.com/freisicht/mite-gsales-importer.git [TARGET FOLDER]
```

or [download](WE_NEED_A_LINK_LOL) this repository first.

Be sure to make the target folder callable for the browser (e.g. `localhost/mite-gsales-importer`).

<details><summary>**Automatic Installation** (click to view)</summary><p>

---

**Requirements before installing**:

1.  Check the access-rights of the folders `/`, `/config` and `/logs`
2.  The database should exist beforehand

Simply open the cloned project in your browser, it will open a form where you can input the details for Mite, gSales and the database.

(( SCREENSHOT AUTOINSTALL FORM ))

After submitting, it will automatically create configuration-files filled with your information.

Done!

Remove the `/config/app.json` file to repeat the process.

---

</p></details>

<details><summary>**Manual Installation** (click to view)</summary><p>

---

Keep an eye for the access-rights for the directories during this whole process, since PHP tries to generate files.

1. Create an empty file `/config/app.json` to stop the automatic installation process.
2. Create an empty file `/log/app.log` for the internal logging mechanism (Kann man nicht einfach das Ding direkt mit in die Repo packen?)
3. Copy `/propel.yaml.dist`, rename it to `propel.yaml` and adjust the database connection information accordingly. Create the database if it does not exist yet.
4. Repeat the process for `config/apis/gsales.json.dist` and `config/apis/mite.json.dist`

After these steps, we need to install the [Composer](https://getcomposer.org/) dependencies and let them do their work! Open the terminal and navigate to the repository folder and follow these last steps:

**Install the composer dependencies**

* if you have installed composer, you can run `composer install`
* otherwise `php composer.phar install`

Sometimes the php-executable is not just `php`, in that case you should look up your php executable and replace it with `php`.

**Install database and create model-files with Propel**

* `vendor/bin/propel convert-conf` — Converts the `propel.yaml` file into a php file
* `vendor/bin/propel migration:diff` — creates a migration file that will modify the database
* `vendor/bin/propel migration:migrate` — applies the migration file (at that point your database should be good to go)
* `vendor/bin/propel model:build` — builds the model-files for PHP
* `php composer.phar dump-autoload` — builds the autoload-file, same composer rules apply for this command

Done!

---

</p></details>

We recommend using some kind of authentication process (e.g. simple http auth) to prevent unauthorized usage.

## Usage

coming soon