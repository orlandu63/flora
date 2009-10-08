<?php
abstract class Memoizer { //todo: rename
	protected static $cache = array();
	public static $overwrite = false;

	//todo: make $cache multidimensional with respect to get_called_class()
	public static function memoize($key, Closure $callback, $overwrite = false) {
		$key = get_called_class() . '-' . $key;
		if(self::$overwrite || $overwrite || !array_key_exists($key, self::$cache)) {
			self::$cache[$key] = $callback();
		}
		return self::$cache[$key];
	}
}