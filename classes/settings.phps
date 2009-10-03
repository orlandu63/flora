<?php
abstract class Settings {
	protected static $settings = array(), $cache = array();
	
	public static function load($file) {
		self::$settings = require $file;
	}
	
	public static function get($setting_string) {
		if(array_key_exists($setting_string, self::$cache)) {
			return self::$cache[$setting_string];
		}
		$settings = explode('/', $setting_string);
		$cursor = self::$settings;
		foreach($settings as $setting) {
			$cursor = $cursor[$setting];
		}
		self::$cache[$setting_string] = $cursor;
		return $cursor;
	}
}