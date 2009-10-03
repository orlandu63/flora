<?php
abstract class Posts {
	public static function getInfo($id, $what = null) {
		$post_info = memoize("p-$id", function() use($id) {
			global $DB;
			return $DB->q('SELECT * FROM posts WHERE id = ?', $id)->fetch();
		});
		return ($what ? $post_info[$what] : $post_info);
	}
	
	public static function getOfTopic($topic) {
		global $DB;
		return $DB->q('SELECT * FROM posts WHERE topic = ?', $topic)->fetchAll();
	}

	public static function make($parent, $author, $body, $topic = null) {
		global $DB;
		if($parent !== null) {
			$topic = self::getInfo($parent, 'topic');
		} elseif($topic === null) {
			throw new InvalidArgumentException('ERROR: LOST CHILD. $parent = ' . $parent);
		}
		$DB->q('INSERT INTO post_info (topic, parent, author, toc, user_id) VALUES(?, ?, ?, UNIX_TIMESTAMP(), ?)',
			$topic, $parent, $author, User::$id);
		$DB->q('INSERT INTO post_data (body) VALUES(?)', $body);
		$post_id = $DB->lastInsertId();
		$DB->q('UPDATE topic_info SET last_post_id = ?, replies = replies + 1 WHERE id = ?',
			$post_id, $topic);
		return self::getInfo($post_id);
	}
	
	public static function exists($id) {
		return (bool)self::getInfo($id);
	}
	
	public static function count() {
		return memoize('p-count', function() {
			global $DB;
			return $DB->q('SELECT COUNT(*) FROM posts')->fetchColumn();
		});
	}
	
	public static function max() {
		return memoize('p-max', function() {
			global $DB;
			return $DB->q('SELECT MAX(id) FROM posts')->fetchColumn();
		});
	}
	
	public static function htmlId($id) {
		return 'p' . $id;
	}
	
	public static function display($id) {
		global $Page;
		$post_info = (is_array($id) ? $id : self::getInfo($id));
		$Page->load('post_view', array(
			'post_info' => $post_info
		));
	}
	
	public static function generatePostClasses(array $post_info) {
		return array('post');
	}
}