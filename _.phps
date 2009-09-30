<?php
define('SOFTWARE', 'flora');
define('VERSION', '1.4-dev');
define('BASE_PATH', 'http://scrap.ath.cx:99/uamb/');
define('CLASS_DIR', 'classes/');
define('DB_NAME', 'flora');

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

$essential_classes = array('page', 'user', 'posts', 'topics', 'inputvalidation', 'form');
array_map('load_class', $essential_classes);

$Page = new Page;
$DB = new DB(DB_NAME);
User::reload();