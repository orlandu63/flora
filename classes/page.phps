<?php
class Page extends STemplator {
	const TEMPLATE_DIR = 'templates/', TEMPLATE_EXT = '.phps';
	const SKELETON_FILE = 'skeleton', OUTPUT_VAR = 'contents';

	const PAGE_TOPIC = 'topic';
	const PAGE_INDEX = 'index';
	const PAGE_POST = 'post';
	const PAGE_SEARCH = 'search';
	const PAGE_HELP = 'help';
	
	const PAGE_SUFFIX = '.php';
	
	const FORM_THREAD = 1;
	const FORM_TOPIC = 2;

	public function __construct() {
		parent::__construct();
		$this->initializeTemplateVars();
		header('Cache-Control: public, max-age=0');
	}
	
	protected function initializeTemplateVars() {
		$this->page_id = '';
		$this->site_nav = array();
	}
	
	public function output() {
		if(!$this->do_output) {
			ob_clean();
			return;
		}
		$this->postProcessSiteNav();
		$this->time_index = xdebug_time_index();
		$this->memory_alloc = (memory_get_peak_usage() >> 10) . 'Kib';
		parent::output();
	}
	
	public function is($id) {
		return (strpos($this->page_id, $id) === 0);
	}
	
	protected function postProcessSiteNav() { //awesome!
		if(!$this->is(Page::PAGE_INDEX)) {
			$this->site_nav = array('Topic Index' => Page::makeURI(Page::PAGE_INDEX)) + $this->site_nav; //prepend Topic Index URI if current page isn't topic index
		}
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

	public static function formatTime($timestamp) {
		$html_format = '<span class="time" title="%s">%s ago</span>';
		
		$seconds = $_SERVER['REQUEST_TIME'] - $timestamp;
		$durations = Time::transformDuration($seconds);
		
		return sprintf($html_format, date(Settings::get('date_format'), $timestamp), implode(', ', $durations));
	}
}