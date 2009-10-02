<?php
define('VERSION', '1.4-dev');
define('CLASS_DIR', 'classes/');
define('SETTINGS_FILE', 'settings.ini');

//these are located inside the include path
require 'db.phps';
require 'stemplator.phps';

function load_class($name) {
	require_once CLASS_DIR . $name . '.phps';
}

function memoize($key, $callback, $overwrite = false) {
	static $cache = array();
	if(!array_key_exists($key, $cache) || $overwrite) {
		$cache[$key] = $callback();
	}
	return $cache[$key];
}

$essential_classes = array('settings', 'page', 'user', 'form', 'posts', 'topics', 'inputvalidation');
array_map('load_class', $essential_classes);

Settings::load(SETTINGS_FILE);

$Page = new Page;
	$Page->set(Settings::get('DEFAULT_TEMPLATE_VARS'));
$DB = new DB(Settings::get('DB_NAME'));
User::load();