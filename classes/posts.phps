<?php
class Posts {
	const MAX_BODY_LENGTH = 8000;
	
	public static function getInfo($id) {
		global $DB;
		return $DB->q('SELECT * FROM posts WHERE id = ?', $id)->fetch();
	}
	
	public static function getOfTopic($topic) {
		global $DB;
		return $DB->q('SELECT * FROM posts WHERE topic = ?', $topic)->fetchAll();
	}

	public static function make($parent, $author, $body, $topic = null) {
		global $DB;
			if($parent !== null) {
				$topic = $DB->q('SELECT topic FROM post_info WHERE id = ?', $parent)->fetchColumn();
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
		global $DB;
		return $DB->q('SELECT 1 FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function count() {
		global $DB;
		return $DB->query('SELECT COUNT(*) FROM post_info')->fetchColumn();
	}
	
	public static function htmlId($id) {
		return 'm' . $id;
	}
	
	public static function display($id) {
		global $Page;
		$post_info = (is_array($id) ? $id : self::getInfo($id));
		if(isset($post_info['id']) && !isset($post_info['topic'])) {
			$post_info['topic'] = self::getTopicById($post_info['id']);
		}
		$Page->load('post_view', array(
			'id' => (isset($post_info['id']) ? self::htmlId($post_info['id']) : ''),
			'post_info' => $post_info
		));
	}
	
	public static function getTopicById($id) {
		global $DB;
		return $DB->q('SELECT topic FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
}