<?php
abstract class Settings {
	protected static $settings = array(), $cache = array();
	
	public static function load($file) {
		self::$settings = require $file;
	}
	
	public static function get($setting_path) {
		if(array_key_exists($setting_path, self::$cache)) {
			return self::$cache[$setting_path];
		}
		$settings = explode('/', $setting_path);
		$cursor = self::$settings;
		foreach($settings as $setting) {
			$cursor = $cursor[$setting];
		}
		self::$cache[$setting_path] = $cursor;
		return $cursor;
	}
}