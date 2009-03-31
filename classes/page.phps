<?php
class Page extends STemplator {
	private $wd;
	
	const FORUM_NAME = 'Flora';
	
	const DEFAULT_ANNOUNCEMENT = 'unmoderated anonymous message board';
	
	const PAGE_TOPIC = 'topic.php';
	const PAGE_INDEX = 'index.php';
	const PAGE_POST = 'post.php';
	
	const FORM_THREAD = 1;
	const FORM_TOPIC = 2;

	public function __construct() {
		$this->wd = getcwd();
		self::$dir = 'templates/';
		self::$ext = '.phps';
		parent::__construct('skeleton');
		$this->announcement = self::DEFAULT_ANNOUNCEMENT;
		ob_start();
	}
	
	public function __destruct() {
		$prev_wd = getcwd();
		chdir($this->wd);
		$this->output();
		chdir($prev_wd);
	}
	
	public function output() {
		$contents = ob_get_clean();
		$this->contents = $contents;
		parent::output();
	}
	
	public static function makeURI($name, array $params = array(), $hash = null) {
		$uri = $name;
		if(!empty($params)) {
			$uri .= '?' . http_build_query($params);
		}
		if($hash) {
			$uri .= '#' . $hash;
		}
		return $uri;
	}
	
	public static function redirect($uri) {
		header('Location: ' . $uri);
	}
	
	public static function displayPostForm($type, array $data = array()) {
		if(empty($data)) {
			$data = array(
				'post' => filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT),
				'author' => User::$name,
				'title' => filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS),
				'body' => filter_input(INPUT_POST, 'body', FILTER_SANITIZE_SPECIAL_CHARS)
			);
		}
		$params = array();
		switch($type) {
			case self::FORM_THREAD:
				$header = 'Reply';
				$params['post'] = $data['post'];
				$legend = 'Post Info';
				$submit_value = 'Post Reply';
				break;
			case self::FORM_TOPIC:
				$header = 'Create a Topic';
				$legend = 'Topic Info';
				$submit_value = 'Make Topic';
				break;
		}
		echo '<h2>', $header , '</h2>',
			'<form action="', self::makeURI(self::PAGE_POST, $params), '" method="post">',
			'<fieldset>',
			'<legend>', $legend , '</legend>',
			'<label>Name: ',
				sprintf('<input type="text" size="%d" value="%s" name="author" maxlength="%1$d"/>',
					User::MAX_AUTHOR_LENGTH,
					$data['author']
				),
			'</label> <small>(optional)</small><br/>';
		if($type === self::FORM_TOPIC) {
			echo '<label>Title: ',
				sprintf('<input type="text" size="%d" value="%s" name="title" maxlength="%1$d"/>',
					Topics::MAX_TITLE_LENGTH,
					$data['title']),
			'</label><br/>';
		}
		echo '<label>Body: (You may use <a href="http://en.wikipedia.org/wiki/Markdown">Markdown</a>)<br/>',
			'<textarea name="body" cols="80" rows="10">', $data['body'], '</textarea>',
			'</label><br/>',
			'<input type="submit" value="',  $submit_value, '" name="submit"/> ',
			'<input type="submit" value="Preview" name="preview"/>',
			'</fieldset>',
			'</form>';
	}
	
	public static function cache($last_modified) {
		$etag = base_convert($last_modified, 10, 36);
		header('Cache-Control: public, must-revalidate, max-age=0');
		header('Last-Modified: ' . date('r', $last_modified));
		header('ETag: ' . $etag);
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
			$durations = array('not long');
		} else {
			$num_durations = count($durations);
			if($num_durations > 2) {
				$durations[$num_durations-1] = 'and ' . $durations[$num_durations-1];
			}
		}
		return sprintf($format, date('r', $timestamp), implode($durations, ', '));
	}
}