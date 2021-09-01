# Just another PHP framework

This code is just another PHP framework to quickly developp data management apps for the web.

# Dependencies

To ease display and ergonomy, this framework includes some open source librairies for PHP and JavaScript :

* [PHPSpreadSheet](https://github.com/PHPOffice/PhpSpreadsheet) PHP lib to generate XLS documents
* [FPDF](http://www.fpdf.org/) PHP lib to generate PDF documents
* [class.upload.php](https://github.com/verot/class.upload.php) PHP lib to manage uploads
* [Bootstrap 5](https://getbootstrap.com/) as CSS framework
* [jQuery](https://jquery.com/) as JavaScript framework
* [moment.js](https://momentjs.com/) JS lib to ease JS date management
* [Chart.js](https://www.chartjs.org/) JS lib to display various charts
* [Full Calendar](https://fullcalendar.io/) JS lib to management planning (for non commercial use)

# Installation

Download the full ZIP archive from Github and copy it on your web server directory.

Works on PHP 7+ and with MySQL 5.7+ / MariaDB 10.2+.

Framework is configured to work with UTF-8 characters encoding.

Import the init_db.sql file into a MySQL/MariaDB database.

By default, there is a table name prefix (prefix_), if you want, before importing SQL dump, you may change this prefix on each table name for each SQL command (search _prefix_ string and replace it by your own string), and specify it in the PHP configuration file (see next chapter).

# Configuration

Edit the _config.php_ file with your files server information (line 68 and 70) and the _database.php_ file with your database server information.
In this file you can toggle debug mode line 5 (true / false ).

You may change the favicon file located in _assets/img/_ directory to fit with your brand.

# First run

Access to your app using the URL defined in the _config.php_ file.

Only one user is defined by default : "admin", with password "admin".

After first connection, change name and password in the _Configuration_ menu (top right gear icon) and _Utilisateurs_ link.

# Customization

This framework needs you to dive into the code for customization.

## Framework files structure

### Main files

* index.php : entry page that calls everything else
* config.php : contains all default app parameters such app directory, items includes, etc.
* database.php : contains database connexion parameters
* controller.php : contains the main app logic and routing
* ajax.php : file that manages ajax calls and generates JSON answers
* init_db.sql : SQL script to run once at installation

### Directories

* assets : contains all assets, JS, CSS and images used by the framework
* classes : contains all PHP classes, including specific item classes overriding Model class
* items : contains items description files (cf. next chapter)
* template : contains display templates
* view : contains static views templates

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
* Affectation, to manage users right on items

To create a new item you need at least to :

1. create the database table corresponding to item description (cf. next chapters)
2. add an item description file into the _items_ directory (cf. next chapters)
3. add the "require_once" line to this file in the config.php file
4. configure it in the items configuration page

## Item database table structure

For each item you need, a table must exists in the database.

This table name has to be coherent with the rest of the code.

This table at least need to have these columns :

* id_{item\_name} (int unsigned auto increment primary key) : column to store the unique ID of the item
* user_cre (int unsigned) : column to store the ID of the item creator (utilisateur)
* date_cre (datetime) : column to store the date and time of item creation
* user_maj (int unsigned) : column to store the item last update date and time
* date_maj (datetime) : column to store the date and time of item update

## Item description file structure

The file has to be named like this : Model{ItemName}.php
Structure of these files is ruled. It's only composed of one variable declaration.
This variable has to be named like this $model_{item_name}. {item_name} will have to be coherent trough the whole code.
This variable has to be a PHP object composed like this ( M = mandatory, O = Optional ) :

* (M) itemName (string) : the code name of the item, wich have to be identical to filename and variable name seen before
* (M) table (string) : the table name corresponding to the item, with prefix if defined before (or concatenate with DBPREF defined variable)
* (M) single (string) : the item display name (singular)
* (M) plural (string) : the item display name (plural)
* (M) columns (array) : an array of column objects (cf. next chapter)
* (O) relations (array) : an array of relation objects (cf. next chapter)
* (O) orderby (string) : the order criteria to display items. Here use SQL syntax with DB column names
* (O) defaultFilters (array) : an array of filters (cf. next chapter)
* (O) objectActions (array) : an array of objectAction objects (cf. next chapter)
* (O) prints (array) : an array of print objects (cf. next chapter)
* (O) readOnlyStates (array) : an array of PHP object describing data conditions to avoid item edit (cf. next chapters)

## Column object structure

The column object will has to be coherent with DB table structure.

It has to be composed at least like this ( M = mandatory, O = Optional ) :

* (M) name (string) : column name in database
* (M) nicename (string) : display name of the field
* (M) grid (object) : Bootstrap grid size to display the field. An objet with 3 porperties :
  1. div (integer) : field container width (12 = full width)
  2. label (integer) : field label width in container
  3. value (integer) : field value width in container
* (M) params (array) : list of HTML input attributes. Only "type" attribute is mandatory (see next chapter for details)
* (M) visible (boolean) : wether the field is visible or not on table list
* (M) editable (boolean) : wether the field is editable or not on edit page
* (M) required (boolean) : wether the field is required or not on edit page
* (O) default (mixed) : the default value for field in new item
* (O) unit (string) : the unit of the field (such as $ or km or kW ...)

### Column type parameter

Here is the different types currently accepted :

* checkbox : display a checkbox control, need to be linked to a boolean column in the database table (stores 0 or 1)
* color : display a colorPicker control, need to be linked to a string column in the database table (stores strings like #0123456)
* date : display a datePicker control, need to be linked to a date column in the database table (stores yyyy-mm-dd values)
* file : display a file input control, need to be linked to a string column in the database table (stores the filename) (you can combined this type with _extensions_ attribute as an array of strings to limit file extensions possibilities)
* image : display a file input control, need to be linked to a string column in the database table (stores the filename) (you can combined this type with _extensions_ attribute as an array of strings to limit file extensions possibilities)
* localisation : display an openstreetmap control and allow to store coordinates and zoom level in database, need to be link to a text column in database table
* number : display a number input control, need to be linked to a nueric column in the database table (stores a number)
* password : display a password control, need to be linked to a 60 chars length string column in the database table (stores bcrypt values)
* text : display a text input control, need to be linked to a string column in the database table (stores strings) (you can combined this type with _auto-complete_ attribute to enable AJAX autocomplete)
* textarea : display a textarea control, need to be linked to a TEXT column in the database table (stores long strings)
* select : display a select input control, need to be linked to a integer column in the database table (stores id of selected element)
  Select input implies a link with another item (i.e. foreign key). So you have to define this link with 3 attributes :
  1. item (string) : name of the linked item
  2. columnKey (string) : column name for select option value (mainly id_{item\_name})
  3. columnLabel (string) : column name for select option label
* calculation : display the result of a calculation (SUM, AVG, COUNT, etc.) used with GROUP BY SQL clause. You have to define this with 2 attributes :
  1.function (string) : the SQL syntax for the calculation
  2.join (string) : optional SQL JOIN syntax to get the data

### Relations

To describe a relation between two items, add a "relation" object to the parent item description file _relations_ property, composed like this :
* item (string) : child item name
* name (string) : nice name to display on screen
* grid (integer) : display width grid (12 = full width)
* static (boolean) : wether the relation use the standard display or not.
  True value implies to create a specific template to display relation, created in template directory and named as edit.{parent\_item\_name}.{child\_item\_name}.php
* displayCondition (boolean) : wether the relation should diplay among condition.
  If true, you need to create a specific class for the item and define a public get_display_condition method returning true or false depending on wished condition
* many (boolean) : wether the relation is many to many or not
  If true, a "Add" button is displayed on the main item relation card element allowing add element via standard modal window

If an item exists only by its parent item (eg. : an order line exists only by its parent order), you need to add a "parentItem" attribute to the item object as a string containing the parent item name

### Actions

In each item description file, you can add a "objectAction" element, as an array containing PHP objects composed like this :

* alias : string, name to be used in code (ie no accent or uppercase or special character)
* nicename: string, action display name
* visible : boolean, determine wether a button is displayed or not on list and edit page
* icon : string, fontawesome icon alias to display on buttons
* color : string, bootstrap colorscheme to display (primary, secondary, dark, light, success, warning, danger )

To make these actions effective, you will need to :

* declare this item "variant" (complexe) in item configuration screen
* create a PHP class extending the existing Model class containing at least a method named as the action alias performing what you need

### Prints

In each item description file, you can add a "print" object to the item description file _prints_ property, composed like this :

* alias : string, name to be used in code (ie no accent or uppercase or special character)
* nicename: string, action display name
* visible : boolean, determine wether a link is displayed or not on edit page
* separator : boolean, determine if the link is just a separator in prints list
* pagination : boolean, determine if the print needs to be paginate

To define each print behavior, you will need to :

* create a PHP file in the template directory named like this print.{item\_name}.{print\_alias}.php

### ReadOnly States

In each item description file, you can add a "readOnlyStates" element, as an array containing PHP objects composed like this :

* column : string, item db column name for state
* values : array, list of values to be considered as readOnly states

### Default filters

In each item description file, you can add a "defaultFilter" object to the item description file _defaultFilters_ property, as an associative array formed like this :

* key : string, db column name for filter
* value : mixed, value filtered
