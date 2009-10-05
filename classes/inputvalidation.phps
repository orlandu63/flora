<?php
define('FILTER_USER', -1);
abstract class InputValidation {
	const VALIDATE_AUTHOR = 1;
	const VALIDATE_BODY = 2;
	const VALIDATE_TITLE = 4;
	
	const SEARCH_MIN_WORD_LENGTH = 3;
	
	protected static $length_exception_format =
		'<strong>%s</strong> must be between %d and %d characters long: its current length is %d character(s).';
	protected static $length_exception_addendum =
		'Note that "&lt;," "&gt;" and "&amp;" are actually 4, 4 and 5 characters in web form, respectively.';
		
	public static function filter_input($type, $variable, $filter = FILTER_DEFAULT, $options = null) {
		return ($type !== FILTER_USER ?
			filter_input($type, $variable, $filter, $options) :
			filter_var($variable, $filter, $options)
		);
	}
	
	public static function validateInt($name, $min = false, $max = false, $type = INPUT_GET) {
		$options = array();
		if($min !== false) {
			$options['min_range'] = $min;
		}
		if($max !== false) {
			$options['max_range'] = $max;
		}
		$options = array('options' => $options);
		$int = self::filter_input($type, $name, FILTER_VALIDATE_INT, $options);
		return ($int !== false ? (int)$int : false);
	}
	
	public static function validateLength($name, $data, $max_length, $min_length = 1) {
		$length = strlen($data);
		if($length > $max_length || $length < $min_length) {
			throw new LengthException(
				sprintf(self::$length_exception_format, ucfirst($name), $min_length, $max_length, $length) .
				($length > $max_length ? '<br/>' . self::$length_exception_addendum : '')
			);
		}
	}
	
	public static function validateAuthor($name, $filter = INPUT_POST) {
		$author = self::filter_input($filter, $name, FILTER_SANITIZE_SPECIAL_CHARS);
		$author = trim($author);
		self::validateLength('name', $author,
			Settings::get('input_thresholds/author/max_length'),
			Settings::get('input_thresholds/author/min_length')
		);
		return $author;
	}
	
	public static function validateBody($name, $filter = INPUT_POST) {
		require_once 'markdown.phps';
		$body = self::filter_input($filter, $name);
		//validate before and after so that this weak server wont have to parse a huge piece of text
		$validateLength = function() use($body) {
			$self = __CLASS__;
			$self::validateLength('body', $body,
				Settings::get('input_thresholds/body/max_length'),
				Settings::get('input_thresholds/body/min_length')
			);
		};
		$validateLength();
		$parser = Markdown::getInstance();
		$body = $parser->transform($body);
		$validateLength();
		return $body;
	}
	
	public static function validateTitle($name, $filter = INPUT_POST) {
		$title = self::filter_input($filter, $name, FILTER_SANITIZE_SPECIAL_CHARS);
		$title = trim($title);
		self::validateLength('title', $title,
			Settings::get('input_thresholds/title/max_length'),
			Settings::get('input_thresholds/title/min_length')
		);
		return $title;
	}
	
	public static function validateQuery($name, $filter = INPUT_POST) {
		$query = self::filter_input($filter, $name, FILTER_SANITIZE_SPECIAL_CHARS);
		$query = trim($query);
		self::validateLength('query', $query, Settings::get('input_thresholds/title/max_length'));
		return $query;
	}
}