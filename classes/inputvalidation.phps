<?php
class InputValidation {
	const VALIDATE_AUTHOR = 1;
	const VALIDATE_BODY = 2;
	const VALIDATE_TITLE = 4;
	
	protected static $length_exception_format =
		'<strong>%s</strong> must be between %d and %d characters long: its current length is %d character(s).';
	protected static $length_exception_addendum =
		'Note that "&lt;," "&gt;" and "&amp;" are actually 4, 4 and 5 characters in web form, respectively.';

	protected static final function hasFlag($flags, $flag) {
		return ($flags & $flag) === $flag;
	}
	
	public static function validate($flags) {
		#this is useless
		$return = array();
		if(self::hasFlag($flags, self::VALIDATE_AUTHOR)) {
			$return['author'] = self::validateAuthor();
		}
		if(self::hasFlag($flags, self::VALIDATE_BODY)) {
			$return['body'] = self::validateBody();
		}
		if(self::hasFlag($flags, self::VALIDATE_TITLE)) {
			$return['title'] = self::validateTitle();
		}
		return (count($return) === 1 ? reset($return) : $return);
	}
	
	protected static function validateLength($name, $data, $max_length, $min_length = 1) {
		$length = strlen($data);
		if($length > $max_length || $length < $min_length) {
			throw new LengthException(
				sprintf(self::$length_exception_format, ucfirst($name), $min_length, $max_length, $length) .
				($length > $max_length ? '<br/>' . self::$length_exception_addendum : '')
			);
		}
	}
	
	public static function validateAuthor($sub = null) {
		$author = ($sub === null ? trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		self::validateLength('name', $author, User::MAX_AUTHOR_LENGTH, 0);
		return $author;
	}
	
	public static function validateBody($sub = null) {
		$body = ($sub === null ? filter_input(INPUT_POST, 'body') : $sub);
		//validate before and after so that this weak server wont have to parse a huge piece of text
		self::validateLength('body', $body, Posts::MAX_BODY_LENGTH, 1);
		$parser = Markdown::getInstance();
		$body = $parser->transform($body);
		self::validateLength('body', $body, Posts::MAX_BODY_LENGTH, 1);
		return $body;
	}
	
	public static function validateTitle($sub = null) {
		$title = ($sub === null ? trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		self::validateLength('title', $title, Topics::MAX_TITLE_LENGTH);
		return $title;
	}
}