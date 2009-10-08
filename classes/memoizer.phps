<?php
abstract class Memoizer { //todo: rename
	protected static $cache = array();
	public static $overwrite = false;

	//todo: make $cache multidimensional with respect to get_called_class()
	public static function memoize($key, Closure $callback, $overwrite = false) {
		$domain = get_called_class();
		if($domain === __CLASS__) {
			$domain = null; //= global domain
		}
		$cache =& self::$cache[$domain][$key];
		if(self::$overwrite || $overwrite || $cache === null) {
			$cache = $callback();
		}
		return $cache;
	}
}