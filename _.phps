<?php
define('VERSION', '1.5-dev');
define('CLASS_DIR', 'classes/');
define('DEPENDANCIES_FILE', 'dependancies.phps');
define('SETTINGS_FILE', 'settings.phps');

//these are located inside the include path
require 'loader.phps';
Loader::loadDepTree('include-dependancies.phps');
foreach(array('DB', 'STemplator', 'Cache', 'HTTP', 'Time') as $include) {
	Loader::load($include);
}

Loader::loadDepTree(DEPENDANCIES_FILE);
$essential_classes = array('Settings', 'Page', 'User', 'Form', 'Posts', 'Topics', 'InputValidation');
foreach($essential_classes as $essential_class) {
	Loader::load($essential_class);
}

Settings::load(SETTINGS_FILE);

$Page = new Page;
	$Page->set(Settings::get('default_template_vars'));
$DB = new DB(Settings::get('db_name'));
User::load();