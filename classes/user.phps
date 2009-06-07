<?php
class User {
	const ANON_NAME = 'anon';
	const MAX_AUTHOR_LENGTH = 10;

	public static $ip, $name, $last_active;
	
	public function __construct() {
		self::$ip = ip2long($_SERVER['REMOTE_ADDR']);
		self::$name = self::getAuthorCookie();
		register_shutdown_function(array(__CLASS__, 'save'));
	}
	
	public static function save() {
		if(self::getAuthorCookie() !== self::$name) {
			setcookie('author', self::$name);
		}
	}
	
	public static function author($author, array $classes = array()) {
		$classes[] = 'author';
		return '<span class="' . implode($classes, ' ') . '">' .
			($author ? $author : self::ANON_NAME) .
		'</span>';
	}
	
	public static function refresh() {
		new self;
	}
	
	public static function isFlooding() {
		global $DB;
		return $DB->q('SELECT 1 FROM post_info WHERE
			ip = ? AND toc >= UNIX_TIMESTAMP() - 10 LIMIT 1', self::$ip)->fetchColumn();
	}

	public static function getAuthorCookie() {
		return filter_input(INPUT_COOKIE, 'author');
	}
}
