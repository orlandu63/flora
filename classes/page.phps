<?php
class Page extends STemplator {
	const FORUM_NAME = 'UAMB';
	
	const DEFAULT_ANNOUNCEMENT =
		'welcome to UAMB, an <del>unmoderated anonymous</del><ins>uber awesome</ins> message board.';
	
	const PAGE_TOPIC = 'topic';
	const PAGE_INDEX = 'index';
	const PAGE_POST = 'post';
	const PAGE_SEARCH = 'search';
	
	const PAGE_SUFFIX = '.php';
	
	const FORM_THREAD = 1;
	const FORM_TOPIC = 2;
	
	static $input_format = '<input type="text" size="%3$d" value="%2$s" name="%1$s" maxlength="%3$d"/>';

	protected $wd;

	public function __construct() {
		$this->wd = getcwd();
		$this->setDirExt('templates/', '.phps');
		parent::__construct('skeleton');
		$this->initializeTemplateVars();
		header('Cache-Control: public, max-age=0');
	}
	
	protected function initializeTemplateVars() {
		$this->page_id = null;
		$this->announcement = self::DEFAULT_ANNOUNCEMENT;
		$this->site_nav = array();
	}
	
	public function __destruct() {
		//because the cwd changes during shutdown,
		//i change the working directory to its previous and then change it back
		$prev_wd = getcwd();
		chdir($this->wd);
		$this->output();
		chdir($prev_wd);
	}
	
	public function output() {
		if(!$this->do_output) {
			ob_clean();
			return;
		}
		$contents = ob_get_clean();
		$this->contents = $contents;
		$this->time_index = xdebug_time_index();
		$this->memory_alloc = (memory_get_peak_usage() >> 10) . 'Kib';
		parent::output();
	}
	
	public static function makeURI($name, array $params = array(), $hash = null, $suffix = self::PAGE_SUFFIX) {
		$uri = $name;
		if($suffix) {
			$uri .= $suffix;
		}
		if(!empty($params)) {
			$uri .= '?' . http_build_query($params);
		}
		if($hash) {
			$uri .= '#' . $hash;
		}
		return $uri;
	}
	
	public static function status($status_code) {
		header('Status: ' . $status_code);
	}
	
	public static function redirect($uri, $status_code) {
		self::status($status_code);
		header('Location: ' . $uri);
	}
	
	public static function error($error, $status_code = null) {
		if($status_code) {
			self::status($status_code);
		}
		echo '<p id="error">', $error, '</p>';
	}
	
		
	public static function HTTPCache($last_modified) {
		$etag = base_convert($last_modified, 10, 36);
		header('Last-Modified: ' . date('r', $last_modified));
		header('ETag: ' . $etag);
	}
	
	public static function makeFingerprintURI($file, array $extra_params = array()) {
		$params = array('v' => base_convert(filemtime($file), 10, 36)) + $extra_params;
		return self::makeURI($file, $params, null, null);
	}
	
	public function terminate() {
		$this->do_output = false;
		die;
	}
	
	public function displayPostForm($type, array $data = array()) {
		if(empty($data)) {
			$data = array(
				'post' => InputValidation::filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT),
				'author' => User::$name,
				'title' => InputValidation::filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS),
				'body' => InputValidation::filter_input(INPUT_POST, 'body', FILTER_SANITIZE_SPECIAL_CHARS),
				'tags' => 'doesn\'t work',
			);
		}
		$params = array();
		$labels = array('author' => 'Name', 'body' => 'Body');
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
				$labels += array('title' => 'title', 'tags' => 'Tags');
				array_push($labels, 'Topic', 'Tags');
				$submit_value = 'Make Topic';
				break;
		}
		$this->load('forms/post', array(
			'header' => $header,
			'params' => $params,
			'legend' => $legend,
			'type' => $type,
			'labels' => $labels,
			'data' => $data,
			'submit_value' => $submit_value
		));
	}
	
	//!!!i need to work on my terminology
	public static function transformDuration($seconds, $max_precision = 2) {
		$periods = array(
			2629743 => 'mth',
			604800 => 'wk',
			86400 => 'day',
			3600 => 'hr',
			60 => 'min'
		);
		$durations = array();
		$precision = 0;
		
		foreach($periods as $seconds_in_period => $period) {
			if($seconds >= $seconds_in_period) {
				$num_periods = (int)($seconds / $seconds_in_period);
				$durations[] = $num_periods . ' ' . $period . ($num_periods === 1 ? '' : 's');
				$seconds -= $num_periods * $seconds_in_period;
			}
			if(!empty($durations) && ++$precision >= $max_precision) {
				break;
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
		
		return $durations;
	}
		
	public static function formatTime($timestamp) {
		$html_format = '<span class="time" title="%s">%s ago</span>';
		
		$seconds = $_SERVER['REQUEST_TIME'] - $timestamp;
		$durations = self::transformDuration($seconds);
		
		return sprintf($html_format, date('r', $timestamp), implode(', ', $durations));
	}
}