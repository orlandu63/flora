<?php
define('VERSION', '1.5-dev');
define('CLASS_DIR', 'classes/');
define('DEPENDENCIES_FILE', 'etc/dependencies.phps');
define('SETTINGS_FILE', 'etc/settings.phps');

//these are located inside the include path
require 'loader.phps';
Loader::loadDepTree('include-dependancies.phps');
foreach(array('Cache', 'DB', 'STemplator', 'HTTP', 'Time') as $include) {
	Loader::load($include);
}

Loader::loadDepTree(DEPENDENCIES_FILE);
$essential_classes = array('Settings', 'Page', 'User', 'Form', 'Posts', 'Topics', 'InputValidation');
foreach($essential_classes as $essential_class) {
	Loader::load($essential_class);
}

Settings::load(SETTINGS_FILE);

$Page = new Page;
	$Page->set(Settings::get('default_template_vars'));
$DB = new DB(Settings::get('db_name'));
User::load();