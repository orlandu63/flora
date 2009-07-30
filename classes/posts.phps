<?php
abstract class Posts {
	const MAX_BODY_LENGTH = 8000;
	const POSTS_PER_SECOND = 0.1;
	
	public static function getInfo($id, $what = null) {
		global $DB;
		$post_info = $DB->q('SELECT * FROM posts WHERE id = ?', $id)->fetch();
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
		$DB->q('INSERT INTO post_info (topic, parent, author, toc, ip) VALUES(?, ?, ?, UNIX_TIMESTAMP(), ?)',
			$topic, $parent, $author, User::$ip);
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
		global $DB;
		return $DB->q('SELECT COUNT(*) FROM post_info')->fetchColumn();
	}
	
	public static function max() {
		global $DB;
		return $DB->q('SELECT MAX(id) FROM post_info')->fetchColumn();
	}
	
	public static function htmlId($id) {
		return 'm' . $id;
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