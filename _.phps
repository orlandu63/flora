<?php
//todo: clean up queries
if(isset($_GET['source'])) {
	die(highlight_file($_SERVER['SCRIPT_FILENAME'], true));
}
require 'utilities.php';
require 'db.php';
require 'stemplator.php';

define('VERSION', '0.5');


class User {
	const ANON_NAME = 'Anon';
	public static $ip, $name;
	
	public function __construct() {
		self::$ip = ip2long($_SERVER['REMOTE_ADDR']);
		self::$name = self::getAuthorCookie();
		register_shutdown_function(array($this, 'save'));
	}
	
	public static function save() {
		if(self::getAuthorCookie() !== self::$name) {
			setcookie('author', self::$name);
		}
	}
	
	public static function refresh() {
		new self;
	}
	
	public static function isFlooding() {
		global $DB;
		return $DB->q('SELECT 1 FROM post_info WHERE
			ip = ? AND toc >= UNIX_TIMESTAMP() - 10 LIMIT 1', self::$ip)->fetchColumn();
	}

	public static function getAuthorCookie() {
		return filter_input(INPUT_COOKIE, 'author');
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

class Post/*  extends ArrayAccessHelper */ {
/* 	protected $info = array();
	protected $array_name = 'info'; */
	
/* 	public function __construct($id) {
		$this->info = self::getInfo($id);
	} */
	
	public static function getInfo($id) {
		global $DB;
		return $DB->q('SELECT * FROM posts WHERE id = ?', $id)->fetch();
	}

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
			$DB->q('UPDATE topic_info SET last_post_id = ?, replies = replies + 1 WHERE id = ?',
				$thread_id, $topic);
		return self::getInfo($thread_id);
	}
	
	public static function exists($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE 1 FROM post_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function display($id) {
		$post = (is_array($id) ? $id : self::getInfo($id));
		echo '<div class="post"><ul class="post-info">',
			'<li>By ', ($post['author'] ? $post['author'] : User::ANON_NAME), '</li>',
			'<li>', Input::formatTime($post['toc']), '</li>',
			'</ul>',
			'<div class="post-body">', $post['body'], '</div></div>';
	}
}

class Topic/* extends ArrayAccessHelper*/ {
/* 	protected $info = array();
	protected $array_name = 'info'; */
	
/* 	public function __construct($id) {
		$this->info = self::getInfo($id);
	} */
	
	public static function getInfo($id) {
		global $DB;
		return $DB->q('SELECT * FROM topics WHERE id = ?', $id)->fetch();
	}
	
	public static function make($title, $author, $body) {
		global $DB;
			$DB->q('INSERT INTO topic_info (title) VALUES(?)', $title);
			$topic_id = $DB->lastInsertId();
			$new_post = Post::make(null, $author, $body, $topic_id);
			$DB->q('UPDATE topic_info SET thread = ? WHERE id = ?', $new_post['id'], $topic_id);
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
	
	public static function getIdFromThread($id) {
		global $DB;
		return $DB->q('SELECT SQL_NO_CACHE topic FROM thread_info WHERE id = ?', $id)->fetchColumn();
	}
	
	public static function link($id = null, $post_id = null) {
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
	
	const MAX_AUTHOR_LENGTH = 10;
	const MAX_BODY_LENGTH = 8000;
	const MAX_TITLE_LENGTH = 80;
	
	
	public static function showContentCreationForm($type, array $data = array()) {
		if(empty($data)) {
			$data = array(
				'thread' => filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT),
				'author' => User::$name,
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
			'<label>Name: <input type="text" size="', Input::MAX_AUTHOR_LENGTH , '" value="', $data['author'], '" name="author" maxlength="10"/></label> <small title="optional">opt</small><br/>';
		if($type === self::FORM_TOPIC) {
			echo '<label>Title: <input type="text" size="', Input::MAX_TITLE_LENGTH , '" value="', $data['title'],'" name="title" maxlength="80"/></label><br/>';
		}
		echo '<label>Body: (You may use <a href="http://en.wikipedia.org/wiki/Markdown">Markdown</a>)<br/>',
			'<textarea name="body" cols="', self::TEXTAREA_COLS , '" rows="', self::TEXTAREA_ROWS, '">', $data['body'], '</textarea></label><br/>',
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
		$author = User::$name = ($sub === null ? trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		if(strlen($author) > Input::MAX_AUTHOR_LENGTH) {
			throw new Exception('Name must be no more than ' . Input::MAX_AUTHOR_LENGTH . ' characters');
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
		if(strlen($body) > Input::MAX_BODY_LENGTH) {
			throw new Exception('Body must be no more than ' . Input::MAX_BODY_LENGTH . ' characters.');
		}
		return $body;
	}
	
	public static function validateTitle($sub = null) {
		$title = ($sub === null ? trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		if(!$title) {
			throw new Exception('Please input a title.');
		}
		if(strlen($title) > Input::MAX_TITLE_LENGTH) {
			throw new Exception('Title must be no more than ' . Input::MAX_TITLE_LENGTH . ' characters.');
		}
		return $title;
	}
	
	public static function formatTime($timestamp, $max_precision = 2) {
		static $format = '<span class="time" title="%s">%s ago</span>';
		static $periods = array(
				2629743 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute',
				1 => 'second'
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