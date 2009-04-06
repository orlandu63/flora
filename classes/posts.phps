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
				$topic = $DB->q('SELECT topic FROM post_info WHERE id = ?', $parent)
					->fetchColumn();
			} elseif($topic === null) {
				throw new InvalidArgumentException('ERROR: LOST CHILD. $parent = ' . $parent);
			}
			$DB->q('INSERT INTO post_info (topic, parent, author, toc, ip) VALUES(?, ?, ?, UNIX_TIMESTAMP(), ?)',
				$topic, $parent, $author, User::$ip);
			$post_id = $DB->lastInsertId();
			$DB->q('INSERT INTO post_data (body) VALUES(?)', $body);
			$DB->q('UPDATE post_info SET num_children = num_children + 1 WHERE id = ?', $parent);
			$DB->q('UPDATE topic_info SET last_post_id = ?, replies = replies + 1 WHERE id = ?',
				$post_id, $topic);
		return self::getInfo($post_id);
	}
	
	public static function exists($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE 1 FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function count() {
		global $DB;
		return $DB->query('SELECT SQL_NO_CACHE COUNT(*) FROM post_info')->fetchColumn();
	}
	
	public static function display($id) {
		$post = (is_array($id) ? $id : self::getInfo($id));
		echo '<div class="post"><ul class="post-info">',
			'<li>By ', User::author($post['author']), '</li>',
			'<li>', Page::formatTime($post['toc']), '</li>';
		if(isset($post['id'], $post['topic'])) {
			echo '<li><a href="', Topics::makeURI($post['topic'], $post['id']), '">Context</a></li>';
		}
		echo '</ul>',
		'<div class="post-body">', $post['body'], '</div></div>';
	}
	
	public static function getTopicById($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE topic FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
}