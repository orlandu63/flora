<?php
require '_.phps';
require 'markdown.php';
$Page->title = 'Post';
$replying_to = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT);
$submit = (bool)filter_input(INPUT_POST, 'submit');
$preview = (bool)filter_input(INPUT_POST, 'preview');
try {
	if(User::isFlooding()) {
		throw new Exception('You can only post once every 10 seconds.');
	}
	if($replying_to && !Posts::exists($replying_to)) {
		throw new Exception('Post does not exist.');
	}

	if($submit || $preview) {
		$author = Input::validate(Input::VALIDATE_AUTHOR);
		$body = Input::validate(Input::VALIDATE_BODY);
		if(!$replying_to) {
			$title = Input::validate(INPUT::VALIDATE_TITLE);
		}
	}
} catch(Exception $exception) {
	echo '<p id="error">',  $exception->getMessage();
	if($exception instanceof LengthException) {
		echo '<br/>Note that "&lt;," "&gt;" and "&amp;" are actually 4, 4 and 5 characters in web form, respectively.';
	}
	echo '</p>';
	return;
}

if($replying_to) {
	$topic_info = Topics::getInfo(Posts::getTopicFromId($replying_to));
	echo '<h3>Replying to: <a href="', Page::PAGE_TOPIC, '?id=', $topic_info['id'], '">',
		$topic_info['title'],
	'</a></h3>';
	Posts::display($replying_to);
}

if($preview) {
	echo '<h3>Preview:';
	if(!$replying_to) {
		echo ' ', $title;
	}
	echo '</h3>';
	Posts::display(array('body' => $body, 'author' => $author, 'toc' => time()));
} elseif($submit) {
	if($replying_to) {
		$new_info = Posts::make($replying_to, $author, $body);
	} else {
		$new_info = Topics::make($title, $author, $body);
	}
	header('HTTP/1.1 303 See Other');
	header('Location: ' . Page::PAGE_TOPIC . '?id=' . $new_info[($replying_to ? 'topic' : 'id')]);
	die;
}

$Page->title .= ' ' . ($replying_to ? 'Thread' : 'Topic');
Input::showContentCreationForm(($replying_to ? Input::FORM_THREAD : Input::FORM_TOPIC));