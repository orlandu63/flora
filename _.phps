<?php
define('SOFTWARE', 'flora');
define('VERSION', '1.4-dev');
define('BASE_PATH', 'http://scrap.ath.cx:99/uamb/');
define('CLASS_DIR', 'classes/');

//these are located inside the include path
require 'db.phps';
require 'stemplator.phps';

function load_class($name) {
	require CLASS_DIR . $name . '.phps';
}

function memoize($key, Closure $callback, $overwrite = false) {
	static $cache = array();
	if(!array_key_exists($key, $cache) || $overwrite) {
		$cache[$key] = $callback();
	}
	return $cache[$key];
}


load_class('page');
load_class('user');
load_class('posts');
load_class('topics');
load_class('inputvalidation');

$Page = new Page;
$DB = new DB('flora');
User::reload();