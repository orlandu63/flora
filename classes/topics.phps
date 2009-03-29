<?php
class Topics {
	const MAX_TITLE_LENGTH = 80;
	
	public static function getInfo($id) {
		global $DB;
		return $DB->q('SELECT * FROM topics WHERE id = ?', $id)->fetch();
	}
	
	public static function getList($page, $per_page) {
		global $DB;
		$per_page = (int)$per_page;
		return $DB->query('SELECT * FROM topics
			ORDER BY is_sticky DESC, last_post_id DESC
			LIMIT ' . ($page * $per_page) . ', ' . $per_page)->fetchAll();
	}
	
	public static function make($title, $author, $body) {
		global $DB;
			$DB->q('INSERT INTO topic_info (title) VALUES(?)', $title);
			$topic_id = $DB->lastInsertId();
			$new_post = Posts::make(null, $author, $body, $topic_id);
			$DB->q('UPDATE topic_info SET post = ? WHERE id = ?', $new_post['id'], $topic_id);
		return self::getInfo($topic_id);
	}
	
	public static function exists($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE 1 FROM topic_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function getTotal() {
		global $DB;
		return $DB->q('SELECT COUNT(*) FROM topic_info')->fetchColumn();
	}
	
	public static function makeURI($id = null, $post_id = null) {
		return Page::makeURI(Page::PAGE_TOPIC, array('id' => $id), ($post_id ? 'm' . $post_id : null));
	}
}