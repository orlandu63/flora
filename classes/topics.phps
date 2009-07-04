<?php
namespace Flora\Topics;
const MAX_TITLE_LENGTH = 80;

function get_info($id) {
	global $DB;
	return $DB->q('SELECT * FROM topics WHERE id = ?', $id)->fetch();
}

function getList($page, $per_page) {
	global $DB;
	$per_page = (int)$per_page;
	return $DB->query('SELECT * FROM topics
		ORDER BY is_sticky DESC, last_post_id DESC
		LIMIT ' . ($page * $per_page) . ', ' . $per_page)->fetchAll();
}

function make($title, $author, $body) {
	global $DB;
		$DB->q('INSERT INTO topic_info (title) VALUES(?)', $title);
		$topic_id = $DB->lastInsertId();
		$new_post = Posts::make(null, $author, $body, $topic_id);
		$DB->q('UPDATE topic_info SET post = ? WHERE id = ?', $new_post['id'], $topic_id);
	return getInfo($topic_id);
}

function exists($id) {
	global $DB;
	return $DB->q('SELECT 1 FROM topic_info WHERE id = ?', $id)->fetchColumn();
}

function count() {
	global $DB;
	return $DB->q('SELECT COUNT(*) FROM topic_info')->fetchColumn();
}

function generateTopicClasses(array $topic_info) {
	$classes = array('topic');
	if($topic_info['is_sticky']) {
		$classes[] = 'sticky';
	}
	return $classes;
}

function makeURI($id, $post_id) {
	return Page::makeURI(Page::PAGE_TOPIC, array('id' => $id), 'm' . $post_id);
}