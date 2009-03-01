<?php
//todo: clean up queries, something with make[post|topic]info (i have no idea what "something" is anymore :[)
if(isset($_GET['source'])) {
	die(highlight_file($_SERVER['SCRIPT_FILENAME'], true));
}
require 'utilities.php';
require 'db.php';
require 'stemplator.php';

define('VERSION', '0.4');


class User {
	public static $ip;
	
	public function __construct() {
		self::$ip = ip2long($_SERVER['REMOTE_ADDR']);
	}
	
	public static function refresh() {
		new self;
	}
	
	public static function isFlooding() {
		global $DB;
		return $DB->q('SELECT 1 FROM post_info WHERE ip = ? AND toc >= UNIX_TIMESTAMP() - 10 LIMIT 1', self::$ip)
			->fetchColumn();
	}
}

#this is fucked up
class Page extends STemplator {
	const DEFAULT_ANNOUNCEMENT = 'unmoderated anonymous message board';
	private $wd;

	public function __construct() {
		$this->wd = getcwd();
		self::$dir = 'templates/';
		self::$ext = '.phps';
		parent::__construct('skeleton');
		ob_start();
	}
	
	public function output() {
		$contents = ob_get_clean();
		$this->contents = $contents;
		parent::output();
	}
	
	public static function cache($last_modified) {
		$etag = base_convert($last_modified, 10, 36);
		header('Cache-Control: public, must-revalidate, max-age=0');
		header('Last-Modified: ' . date('r', $last_modified));
		header('ETag: ' . $etag);
	}
	
	public function __destruct() {
		$prev_wd = getcwd();
		chdir($this->wd);
		$this->output();
		chdir($prev_wd);
	}
}

class Post {
	const MAX_AUTHOR_LENGTH = 10;
	const MAX_BODY_LENGTH = 8000;

	public static function make($parent, $author, $body, $topic = null) {
		global $DB;
			if($parent !== null) {
				$topic = $DB->q('SELECT topic FROM post_info WHERE id = ?', $parent)
					->fetchColumn();
			} elseif($topic === null) {
				die("ERROR: LOST CHILD. \$parent = $parent, \$topic = $topic");
			}
			$DB->q('INSERT INTO post_info (topic, parent, author, toc, ip) VALUES(?, ?, ?, UNIX_TIMESTAMP(), ?)',
				$topic, $parent, $author, User::$ip);
			$thread_id = $DB->lastInsertId();
			$DB->q('INSERT INTO post_data (body) VALUES(?)', $body);
			$DB->q('UPDATE post_info SET num_children = num_children + 1 WHERE id = ?', $parent);
			$DB->q('UPDATE topic_info SET last_post_id = ?, replies = replies + 1 WHERE id = ?', $thread_id, $topic);
		return array('topic' => $topic, 'thread' => $thread_id);
	}
	
	public static function exists($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE 1 FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function getInfo($id) {
		global $DB;
		return $DB->q('SELECT post_info.id id, topic, parent, author, toc, ip, num_children, body
			FROM post_info
				LEFT JOIN post_data ON post_info.id = post_data.id
			WHERE post_info.id = ?', $id)->fetch();
	}
	
	public static function display($id) {
		$post = is_array($id) ? $id : self::getInfo($id);
		echo '<div class="post"><ul class="postinfo">',
			'<li>By ', ($post['author'] ? $post['author'] : 'Anon'), '</li>',
			'<li>', Input::formatTime($post['toc']), '</li>',
			'</ul>',
			$post['body'], '</div>';
	}
}

class Topic {
	const MAX_TITLE_LENGTH = 80;
	
	public static function make($title, $author, $body) {
		global $DB;
			$DB->q('INSERT INTO topic_info (title) VALUES(?)', $title);
			$topic_id = $DB->lastInsertId();
			$new_info = Post::make(null, $author, $body, $topic_id);
			$DB->q('UPDATE topic_info SET thread = ? WHERE id = ?', $new_info['thread'], $topic_id);
		return $new_info;
	}
	
	public static function exists($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE 1 FROM topic_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function getTotal() {
		global $DB;
		return $DB->q('SELECT COUNT(*) FROM topic_info')->fetchColumn();
	}
	
	public static function getInfo($id) {
		global $DB;
		return $DB->q('SELECT topic_info.id id, thread, title, last_post_id, last_post_info.toc last_post, post_info.author author, post_info.toc, post_info.ip, post_info.num_children
			FROM topic_info
				LEFT JOIN post_info ON topic_info.id = post_info.topic
				LEFT JOIN post_info last_post_info ON topic_info.last_post_id = last_post_info.id
			WHERE topic_info.id = ?', $id)->fetch();
	}
	
	public static function getIdFromThread($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE topic FROM thread_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function link($id, $post_id = null) {
		return 'topic.php?id=' . $id . ($post_id ? '#m' . $post_id : '');
	}
}

class Input {
	const TEXTAREA_COLS = 80;
	const TEXTAREA_ROWS = 10;
	const FORM_THREAD = 1;
	const FORM_TOPIC = 2;
	const VALIDATE_AUTHOR = 1;
	const VALIDATE_BODY = 2;
	const VALIDATE_TITLE = 4;
	
	public static function showContentCreationForm($type, array $data = array()) {
		if(empty($data)) {
			$data = array(
				'thread' => filter_input(INPUT_GET, 'thread', FILTER_SANITIZE_NUMBER_INT),
				'author' => self::getAuthorCookie(),
				'title' => filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS),
				'body' => filter_input(INPUT_POST, 'body', FILTER_SANITIZE_SPECIAL_CHARS)
			);
		}
		switch($type) {
			case self::FORM_THREAD:
				$header = 'Reply';
				$action = 'post.php?thread=' . $data['thread'];
				$legend = 'Post Info';
				$submit_value = 'Post Reply';
				break;
			case self::FORM_TOPIC:
				$header = 'Start a Topic';
				$action = 'post.php';
				$legend = 'Topic Info';
				$submit_value = 'Make Topic';
				break;
		}
		echo '<h2>', $header , '</h2>',
			'<form action="', $action , '" method="post">',
			'<fieldset>',
			'<legend>', $legend , '</legend>',
			'Name: <input type="text" size="', Post::MAX_AUTHOR_LENGTH , '" value="', $data['author'], '" name="author" maxlength="10"/> <small>opt</small><br/>';
		if($type === self::FORM_TOPIC) {
			echo 'Title: <input type="text" size="', Topic::MAX_TITLE_LENGTH , '" value="', $data['title'],'" name="title" maxlength="80"/><br/>';
		}
		echo 'Body: (You may use <a href="http://en.wikipedia.org/wiki/Markdown">Markdown</a>)<br/>',
			'<textarea name="body" cols="', self::TEXTAREA_COLS , '" rows="', self::TEXTAREA_ROWS, '">', $data['body'], '</textarea><br/>',
			'<input type="submit" value="',  $submit_value, '" name="submit"/> ',
			'<input type="submit" value="Preview" name="preview"/>',
			'</fieldset>',
			'</form>';
	}

	public static function validate($flags) {
		#this is useless
		$return = array();
		if(has_flag($flags, self::VALIDATE_AUTHOR)) {
			$return['author'] = self::validateAuthor();
		}
		if(has_flag($flags, self::VALIDATE_BODY)) {
			$return['body'] = self::validateBody();
		}
		if(has_flag($flags, self::VALIDATE_TITLE)) {
			$return['title'] = self::validateTitle();
		}
		return (count($return) === 1 ? reset($return) : $return);
	}
	
	public static function validateAuthor($sub = null) {
		$author = ($sub === null ? trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		if(self::getAuthorCookie() !== $author) {
			setcookie('author', $author);
		}
		if(strlen($author) > Post::MAX_AUTHOR_LENGTH) {
			throw new Exception('Name must be no more than ' . Post::MAX_AUTHOR_LENGTH . ' characters');
		}
		return $author;
	}
	
	public static function validateBody($sub = null) {
		$body = ($sub === null ? trim(filter_input(INPUT_POST, 'body')) : $sub);
		if(!$body) {
			throw new Exception('Body must be at least 1 char');
		}
		$parser = Markdown::getInstance();
		$body = $parser->transform($body);
		if(strlen($body) > Post::MAX_BODY_LENGTH) {
			throw new Exception('Body must be no more than ' . Post::MAX_BODY_LENGTH . ' characters.');
		}
		return $body;
	}
	
	public static function validateTitle($sub = null) {
		$title = ($sub === null ? trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		if(!$title) {
			throw new Exception('Please input a title.');
		}
		if(strlen($title) > Topic::MAX_TITLE_LENGTH) {
			throw new Exception('Title must be no more than ' . Topic::MAX_TITLE_LENGTH . ' characters.');
		}
		return $title;
	}

	public static function getAuthorCookie() {
		return filter_input(INPUT_COOKIE, 'author');
	}
	
	public static function formatTime($timestamp, $max_precision = 2) {
		static $format = '<span class="time" title="%s">%s ago</span>';
		static $periods = array(
				2629743 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute'
		);
		
		$seconds = time() - $timestamp;

		$durations = array();
		$precision = 0;

		foreach ($periods as $seconds_in_period => $period) {
			if($seconds >= $seconds_in_period) {
				$num_periods = (int)($seconds / $seconds_in_period);
				$durations[] = $num_periods . ' ' . $period . ($num_periods === 1 ? '' : 's');
				$seconds -= $num_periods * $seconds_in_period;
				if(++$precision >= $max_precision) {
					break;
				}
			}
		}

		if(empty($durations)) {
			$durations = array('a few seconds');
		} else {
			$num_durations = count($durations);
			if($num_durations > 2) {
				$durations[$num_durations-1] = 'and ' . $durations[$num_durations-1];
			}
		}
		return sprintf($format, date('c', $timestamp), implode($durations, ', '));
	}
}

$DB = new DB('flora');
$Page = new Page;
User::refresh();