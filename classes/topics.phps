<?php
abstract class Topics {
	public static function getInfo($id, $what = null) {
		global $DB;
		$topic_info = $DB->q1('SELECT * FROM topics WHERE id = ?', $id);
		return ($what ? $topic_info[$what] : $topic_info);
	}
	
	public static function getList($page, $per_page) {
		global $DB;
		$per_page = (int)$per_page;
		return $DB->qa('SELECT * FROM topics
			ORDER BY is_sticky DESC, last_post_id DESC
			LIMIT ' . ($page * $per_page) . ', ' . $per_page);
	}
	
	public static function make($title, $author, $body) {
		global $DB;
			$DB->q('INSERT INTO topic_info (title) VALUES(?)', $title);
			$topic_id = $DB->lastInsertId();
			$new_post = Posts::make(null, $author, $body, $topic_id);
		return self::getInfo($topic_id);
	}
	
	public static function search($text) {
		global $DB;
		return $DB->qa('SELECT * FROM topics WHERE MATCH(title) AGAINST(? IN NATURAL LANGUAGE MODE)', $text);
	}
	
	public static function exists($id) {
		return (bool)self::getInfo($id);
	}
	
	public static function count() {
		global $DB;
		return $DB->qc('SELECT COUNT(*) FROM topics');
	}
	
	public static function max() {
		global $DB;
		return $DB->qc('SELECT MAX(id) FROM topics');
	}

	public static function htmlId($id) {
		return 't' . $id;
	}

	public static function generateTopicClasses(array $topic_info) {
		$classes = array('topic');
		if($topic_info['is_sticky']) {
			$classes[] = 'sticky';
		}
		return $classes;
	}
	
	public static function makeURI($id, $post_id) {
		return Page::makeURI(Page::PAGE_TOPIC, array('id' => $id), Posts::htmlId($post_id));
	}
}