<?php
abstract class User {
	const AUTHOR_COOKIE = 'author';

	public static $id, $name;

	public static function load() {
		self::$id = substr(hash('md5', ip2long($_SERVER['REMOTE_ADDR'])), 0, Settings::get('user/id_length'));
		self::$name = substr(self::getAuthor(), 0, Settings::get('input_thresholds/author/max_length'));
		register_shutdown_function(array(__CLASS__, 'save'));
	}

	public static function save() {
		if(self::$name !== self::getAuthor()) {
			setcookie(self::AUTHOR_COOKIE, self::$name, $_SERVER['REQUEST_TIME'] + 60 * 60 * 24 * 365);
		}
	}
	
	public static function display($author, $id, array $classes = array()) {
		$classes = array_merge(self::generateUserClasses($author, $id), $classes);
		return sprintf('<span class="%s" title="id: %s">%s</span>',
			implode(' ', $classes), $id, ($author ?: Settings::get('user/anon_name'))
		);
	}

	public static function isAdmin($id) {
		return Cache::memoize(array(__CLASS__, $id, 'is-admin'), function() use($id) {
			return in_array($id, Settings::get('admin_ids'));
		});
	}
	
	public static function isFlooding($id = null) {
		global $DB;
		$id = ($id ?: self::$id);
		return (bool)$DB->qc('SELECT 1 FROM posts WHERE user_id = ? AND toc >= UNIX_TIMESTAMP() - ? LIMIT 1',
			array($id, (1 / Settings::get('input_thresholds/posts_per_second'))));
	}
	
	public static function generateUserClasses($author, $id) {
		$classes = array('user');
		if(self::isAdmin($id)) {
			$classes[] = 'admin';
		}
		return $classes;
	}

	public static function getAuthor() {
		return InputValidation::filter_input(INPUT_COOKIE, self::AUTHOR_COOKIE);
	}
}