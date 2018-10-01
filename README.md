# Just another PHP framework

This code is just another PHP framework to quickly developp data management apps for the web.

# Dependencies

To ease display and ergonomy, this framework includes some open source librairies for PHP and JavaScript :

* [PHPSpreadSheet](https://github.com/PHPOffice/PhpSpreadsheet) PHP lib to generate XLS documents
* [FPDF](http://www.fpdf.org/) PHP lib to generate PDF documents
* [Telechargement](https://php.developpez.com/telecharger/detail/id/2944/Classe-Php-d-upload-de-fichiers-avec-options-de-redimensionnement-renommage-gestion-des-erreurs) PHP lib to manage uploads
* [Bootstrap 3.3.7](https://getbootstrap.com/docs/3.3/) as CSS framework
* [jQuery](https://jquery.com/) as JavaScript framework
* [moment.js](https://momentjs.com/) JS lib to ease JS date management
* [Full Calendar](https://fullcalendar.io/) JS lib to management planning
* Full Caldendar Scheduler, a Full Calendar plugin to manage timeline view

# Installation

Download the full ZIP archive from Github and copy it on your web server directory.

Works on PHP 7 or newer and with MySQL 5.7+ / MariaDB 10.2+.

Framework is configured to work with UTF-8 characters encoding.

Import the init_db.sql file into a MySQL/MariaDB database.

By default, there is no table name prefix, if you want, before importing SQL dump, you may add a prefix on each table name for each CREATE TABLE commands, and specify it in the PHP configuration file (see next chapter).

# Configuration

Edit the _config.php_ file with your files server information (line 68 and 70) and your database server information (line 98 to 108).

You may change the favicon file located in _assets/img/_ directory to fit with your brand.

# First run

Access to your app using the URL defined in the _config.php_ file.

Only one user is defined by default : "admin", with password "admin".

# Customization

This framework needs you to dive into the code for customization.

## Framework files structure

### Main files

* index.php : entry page that calls everything else
* config.php : contains all default app parameters such app directory, items includes, database parameters, etc.
* controller.php : contains the main app logic and routing
* ajax.php : file that manages ajax calls and generates JSON answers
* init_db.sql : SQL script to run once at installation

### Directories

* assets : contains all assets, JS, CSS and images used by the framework
* classes : contains all PHP classes, including specific item classes overriding Model class
* items : contains items description files (cf. next chapter)
* template : contains display templates
* view : contains specific static views templates

## Items

This framework logic is based on "items". An item is an object type (or entity).

Basic existing and mandatory items are :

* Item, to manage app items
* Option, to manage app global options
* Utilisateur, to manage app users
* Groupe, to manage groups of users with differents rights
* Etat, to manage items state, to use a workflow for example
* Document, to manage documents generate by the app
* TypeDocument, to manage differents document types
* Analyse, to manage extraction queries
* TypeAnalyse, to manage differents analysis types

To create a new item you need at least to :

1. create the database table corresponding to item description (cf. next chapters)
2. add an item description file into the items/ directory (cf. next chapters)
3. add the "require" line to this file in the config.php file
4. configure it in the items management screen

## Item database table structure

For each item you need, a table must exists in the database.

This table name has to be coherent with the rest of the code.

This table at least need to have these columns :

* id_{item\_name} (int unsigned auto increment primary key) : column to store the unique ID of the item
* user_cre (int unsigned) : column to store the ID of the item creator (utilisateur)
* date_cre (datetime) : column to store the date and time of item creation
* user_maj (int unsigned) : column to store the item last update date and time

## Item description file structure

The file has to be named like this : Model{ItemName}.php
Structure of these files is ruled. It's only composed of one variable declaration.
This variable has to be named like this $model_{item_name}. {item_name} will have to be coherent trough the whole code.
This variable has to be a PHP object composed like this :

* itemName (string) : the code name of the item, wich have to be coherent to filename and variable name
* table (string) : the table name corresponding to the item, with prefix if necessary (or DBPREF defined variable)
* single (string) : the item display name (singular)
* plural (string) : the item display name (plural)
* orderby (string) : the order criteria to display items. Here use SQL syntax with DB column names
* columns (array) : an array of column objects (cf. next chapter)

## Column object structure

The column object will has to be coherent with DB table structure.

It has to be composed at least like this :

* name (string) : column name in database
* nicename (string) : display name of the field
* grid (object) : Bootstrap grid size to display the field.
  1. div (integer) : field container width (12 = full width)
  2. label (integer) : field label width in container
  3. value (integer) : field value width in container
* params (array) : list of HTML input attributes. Only "type" attribute is mandatory (see next chapter for details)
* visible (boolean) : wether the field is visible or not on table list
* editable (boolean) : wether the field is editable or not on edit page
* required (boolean) : wether the field is required or not on edit page

### Column type parameter

Here is the different types currently accepted :

* checkbox : display a checkbox control, need to be linked to a boolean column in the database table (stores 0 or 1)
* color : display a colorPicker control, need to be linked to a string column in the database table (stores strings like #0123456)
* date : display a datePicker control, need to be linked to a date column in the database table (stores yyyy-mm-dd values)
* image : display a file input control, need to be linked to a string column in the database table (stores the filename)
* number : display a number input control, need to be linked to a nueric column in the database table (stores a number)
* password : display a password control, need to be linked to a 60 chars length string column in the database table (stores bcrypt values)
* text : display a text input control, need to be linked to a string column in the database table (stores strings)
* textarea : display a textarea control, need to be linked to a TEXT column in the database table (stores long strings)
* select : display a select input control, need to be linked to a integer column in the database table (stores id of selected element)
  Select input implies a link with another item (i.e. foreign key). So you have to define this link with 3 attributes :
  1. item (string) : name of the linked item
  2. columnKey (string) : column name for select option value (mainly id_{item\_name})
  3. columnLabel (string) : column name for select option text

### Parent/Child Relations

If an item exists only by its parent item (eg. : an order line exists only by its parent order). You have to set a relation.

To do so, you need to add :

* a "parentItem" attribute to the item object as a string containing the parent item name
* a "relations" attribute to the parent item object containing an array of objects, each object represents a relation, with the following attributes :
  * item (string) : child item name
  * name (string) : nice name to display on screen
  * grid (integer) : display width grid (12 = full width)
  * static (boolean) : wether the relation use the standard display or not. True imply to create a specific template to display relation

### Actions


### Prints


# TODO

Implement read-only states to avoid editing item with a certain state (Etat).
