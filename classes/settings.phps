<?php
abstract class Settings {
	protected static $settings = array();
	
	public static function load($file) {
		self::$settings = require $file;
	}
	
	public static function get($setting) {
		return self::$settings[$setting];
	}
}