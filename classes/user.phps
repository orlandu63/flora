<?php
class User {
	const ANON_NAME = 'anon';
	const MAX_AUTHOR_LENGTH = 10;
	const ID_LENGTH = 6;

	public static $id, $name;
	
	private function __construct() {
		self::$id = substr(hash('md5', ip2long($_SERVER['REMOTE_ADDR'])), 0, self::ID_LENGTH);
		self::$name = substr(self::getAuthor(), 0, self::MAX_AUTHOR_LENGTH);
		register_shutdown_function(array(__CLASS__, 'save'));
	}
	
	public static function save() {
		if(self::getAuthor() !== self::$name) {
			setcookie('author', self::$name, $_SERVER['REQUEST_TIME'] + 60 * 60 * 24 * 365);
		}
	}
	
	public static function display($author, $id, array $classes = array()) {
		$classes = array_merge(self::generateUserClasses(), $classes);
		return sprintf('<span class="%s" title="id: %s">%s</span>',
			implode(' ', $classes),
			$id,
			($author ?: self::ANON_NAME)
		);
	}
	
	public static function reload() {
		new self;
	}
	
	public static function isFlooding() {
		global $DB;
		return $DB->q('SELECT 1 FROM post_info WHERE user_id = ? AND toc >= UNIX_TIMESTAMP() - ? LIMIT 1',
			self::$id, (1 / Posts::POSTS_PER_SECOND))->fetchColumn();
	}
	
	public static function generateUserClasses() {
		return array('user');
	}

	public static function getAuthor() {
		return filter_input(INPUT_COOKIE, 'author');
	}
}
