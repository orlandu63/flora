<?php
class User {
	const ANON_NAME = 'anon';
	const MAX_AUTHOR_LENGTH = 10;

	public static $ip, $name;
	
	private function __construct() {
		self::$ip = ip2long($_SERVER['REMOTE_ADDR']);
		self::$name = self::getAuthorCookie();
		register_shutdown_function(array(__CLASS__, 'save'));
	}
	
	public static function save() {
		if(self::getAuthorCookie() !== self::$name) {
			setcookie('author', self::$name, $_SERVER['REQUEST_TIME'] + 60 * 60 * 24 * 365);
		}
	}
	
	public static function author($author, array $classes = array()) {
		if(empty($classes)) {
			$classes = self::generateUserClasses();
		}
		return sprintf('<span class="%s">%s</span>', implode(' ', $classes), ($author ?: self::ANON_NAME));
	}
	
	public static function reload() {
		new self;
	}
	
	public static function isFlooding() {
		global $DB;
		return $DB->q('SELECT 1 FROM post_info WHERE ip = ? AND toc >= UNIX_TIMESTAMP() - ? LIMIT 1',
			self::$ip, 1 / Posts::POSTS_PER_SECOND)->fetchColumn();
	}
	
	public static function generateUserClasses() {
		return array('user');
	}

	public static function getAuthorCookie() {
		return filter_input(INPUT_COOKIE, 'author');
	}
}
