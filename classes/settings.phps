<?php
abstract class Settings {
	protected static $settings = array();
	
	public static function load($file) {
		self::$settings = require $file;
	}
	
	public static function get($setting) {
		$settings = explode('/', $setting);
		$cursor = self::$settings;
		foreach($settings as $setting) {
			$cursor = $cursor[$setting];
		}
		return $cursor;
	}
}