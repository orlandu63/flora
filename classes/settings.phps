<?php
abstract class Settings {
	const CACHE_PREFIX = 's';

	protected static $settings = array();
	
	public static function load($file) {
		self::$settings = require $file;
	}
	
	public static function get($setting_path) {
		return Cache::memoize(array(self::CACHE_PREFIX, $setting_path), function() use($setting_path) {
			$self = __CLASS__;
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