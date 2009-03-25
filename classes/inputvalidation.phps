<?php
class InputValidation {
	const VALIDATE_AUTHOR = 1;
	const VALIDATE_BODY = 2;
	const VALIDATE_TITLE = 4;
	
	protected static $length_exception_format = '<strong>%s</strong> must be no greater than %s characters long: its current length is %d characters.';

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
	
	protected static function validateLength($name, $data, $max_length) {
		$length = strlen($data);
		if($length > $max_length) {
			throw new LengthException(sprintf(self::$length_exception_format,
				ucfirst($name), $max_length, $length));
		}
	}
	
	public static function validateAuthor($sub = null) {
		$author = ($sub === null ? trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		self::validateLength('name', $author, User::MAX_AUTHOR_LENGTH);
		User::$name = $author;
		return $author;
	}
	
	public static function validateBody($sub = null) {
		$body = ($sub === null ? trim(filter_input(INPUT_POST, 'body')) : $sub);
		if(!$body) {
			throw new Exception('Please input a body.');
		}
		$parser = Markdown::getInstance();
		$body = $parser->transform($body);
		self::validateLength('body', $body, Posts::MAX_BODY_LENGTH);
		return $body;
	}
	
	public static function validateTitle($sub = null) {
		$title = ($sub === null ? trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) : $sub);
		if(!$title) {
			throw new Exception('Please input a title.');
		}
		self::validateLength('title', $title, Topics::MAX_TITLE_LENGTH);
		return $title;
	}
}
