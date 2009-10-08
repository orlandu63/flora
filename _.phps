<?php
define('VERSION', '1.4');
define('CLASS_DIR', 'classes/');
define('SETTINGS_FILE', 'settings.phps');

//these are located inside the include path
require 'db.phps';
require 'stemplator.phps';
require 'time.phps';

function load_class($name) {
	require_once CLASS_DIR . $name . '.phps';
}

$essential_classes = array('memoizer', 'settings', 'page', 'user', 'form', 'posts', 'topics', 'inputvalidation');
foreach($essential_classes as $essential_class) {
	load_class($essential_class);
}

Settings::load(SETTINGS_FILE);

$Page = new Page;
	$Page->set(Settings::get('default_template_vars'));
$DB = new DB(Settings::get('db_name'));
User::load();