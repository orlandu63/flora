<?php
abstract class User {
	const ANON_NAME = 'anon';
	const MAX_AUTHOR_LENGTH = 10;
	const ID_LENGTH = 6;

	public static $id, $name;
	
	public static function save() {
		if(self::getAuthor() !== self::$name) {
			setcookie('author', self::$name, $_SERVER['REQUEST_TIME'] + 60 * 60 * 24 * 365);
		}
	}
	
	public static function display($author, $id, array $classes = array()) {
		$classes = array_merge(self::generateUserClasses($author, $id), $classes);
		return sprintf('<span class="%s" title="id: %s">%s</span>',
			implode(' ', $classes), $id, ($author ?: self::ANON_NAME)
		);
	}
	
	public static function load() {
		self::$id = substr(hash('md5', ip2long($_SERVER['REMOTE_ADDR'])), 0, self::ID_LENGTH);
		self::$name = substr(self::getAuthor(), 0, self::MAX_AUTHOR_LENGTH);
		register_shutdown_function(array(__CLASS__, 'save'));
	}
	
	public static function isFlooding() {
		$id = self::$id;
		return memoize('user-flooding', function() use($id) {
			global $DB;
			return (bool)$DB->q('SELECT 1 FROM posts WHERE user_id = ? AND toc >= UNIX_TIMESTAMP() - ? LIMIT 1',
				$id, (1 / Posts::POSTS_PER_SECOND))->fetchColumn();
		});
	}
	
	public static function generateUserClasses($author, $id) {
		$classes = array('user');
		if($id === '63dc48') { //that's me!
			$classes[] = 'admin';
		}
		return $classes;
	}

	public static function getAuthor() {
		return InputValidation::filter_input(INPUT_COOKIE, 'author');
	}
}
