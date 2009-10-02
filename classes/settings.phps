<?php
abstract class Settings {
	static $settings = array();
	
	public static function load($file) {
		self::$settings = parse_ini_file($file, true);
	}
	
	public static function get($setting) {
		return self::$settings[$setting];
	}
}