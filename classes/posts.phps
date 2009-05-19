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
		return $DB->q('SELECT SQL_NO_CACHE 1 FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function count() {
		global $DB;
		return $DB->query('SELECT SQL_NO_CACHE COUNT(*) FROM post_info')->fetchColumn();
	}
	
	public static function htmlId($id) {
		return 'm' . $id;
	}
	
	public static function display($id) {
		$post = (is_array($id) ? $id : self::getInfo($id));
		echo '<div class="post" id="', (isset($post['id']) ? self::htmlId($post['id']) : ''), '">',
		'<div class="post-info-wrap">', '<ul class="post-info">',
			'<li>by ', User::author($post['author']), '</li>',
			'<li>', Page::formatTime($post['toc']), '</li>';
			if(isset($post['id'])) {
				if(!isset($post['topic'])) {
					$post['topic'] = self::getTopicById($post['id']);
				}
				echo '<li>',
					sprintf('<a href="%s" title="view context of this post">context</a>',
						Topics::makeURI($post['topic'], $post['id'])),
				'</li>';
			}
		echo '</ul></div>',
		'<div class="post-body">', $post['body'], '</div></div>';
	}
	
	public static function getTopicById($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE topic FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
}