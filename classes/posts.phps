<?php
abstract class Posts {
	public static function getInfo($id, $what = null) {
		global $DB;
		$post_info = $DB->q1('SELECT * FROM posts WHERE id = ?', $id);
		return ($what ? $post_info[$what] : $post_info);
	}
	
	public static function getOfTopic($topic) {
		global $DB;
		return $DB->qa('SELECT * FROM posts WHERE topic = ?', $topic);
	}

	public static function make($parent, $author, $body, $topic = null) {
		global $DB;
		if($parent !== null) {
			$topic = self::getInfo($parent, 'topic');
		} elseif($topic === null) {
			throw new InvalidArgumentException('ERROR: LOST CHILD. $parent = ' . $parent);
		}
		$DB->q('INSERT INTO post_info (topic, parent, author, toc, user_id) VALUES(?, ?, ?, UNIX_TIMESTAMP(), ?)',
			array($topic, $parent, $author, User::$id));
		$DB->q('INSERT INTO post_data (body) VALUES(?)', $body);
		$post_id = $DB->lastInsertId();
		return self::getInfo($post_id);
	}
	
	public static function exists($id) {
		return (bool)self::getInfo($id);
	}
	
	public static function count() {
		global $DB;
		return $DB->qc('SELECT COUNT(*) FROM posts');
	}
	
	public static function max() {
		global $DB;
		return $DB->qc('SELECT MAX(id) FROM posts');
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