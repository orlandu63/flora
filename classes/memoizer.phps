<?php
abstract class Memoizer {
	protected static $cache = array();
	public static $overwrite = false;

	public static function memoize($key, Closure $callback, $overwrite = false) {
		$key = get_called_class() . '-' . $key;
		if(self::$overwrite || $overwrite || !array_key_exists($key, self::$cache)) {
			self::$cache[$key] = $callback();
		}
		return self::$cache[$key];
	}
}