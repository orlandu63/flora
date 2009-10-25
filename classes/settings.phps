<?php
abstract class Settings {
	protected static $settings = array();
	
	public static function load($file) {
		self::$settings = require $file;
	}
	
	public static function get($setting_path) {
		return Cache::memoize(array(__CLASS__, $setting_path), function($self = __CLASS__) use($setting_path) {
			return $self::getSettingFromPath($setting_path);
		});
	}
	
	public static function getSettingFromPath($setting_path) {
		$settings = explode('/', $setting_path);
		$cursor = self::$settings;
		foreach($settings as $setting) {
			$cursor = $cursor[$setting];
		}
		return $cursor;
	}
}