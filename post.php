<?php
require '_.phps';
require CLASS_DIR . 'inputvalidation.phps';
require 'markdown.php';

$Page->pageId = Page::PAGE_POST;
$Page->title = 'Post';
$replying_to = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT);
$making_topic = !$replying_to;
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
		$author = InputValidation::validate(InputValidation::VALIDATE_AUTHOR);
		$body = InputValidation::validate(InputValidation::VALIDATE_BODY);
		if($making_topic) {
			$title = InputValidation::validate(InputValidation::VALIDATE_TITLE);
		}
	}
	
	$valid = true;
} catch(Exception $exception) {
	echo '<p id="error">',  $exception->getMessage(), '</p>';
	$valid = false;
}

if($valid) {
	if($replying_to) {
		$topic_info = Topics::getInfo(Posts::getTopicById($replying_to));
		echo '<h3>Replying to: <a href="', Topics::makeURI($topic_info['id']), '">',
			$topic_info['title'],
		'</a></h3>';
		Posts::display($replying_to);
	}

	if($preview) {
		echo '<h3>Preview:';
		if($making_topic) {
			echo ' ', $title;
		}
		echo '</h3>';
		Posts::display(array('body' => $body, 'author' => $author, 'toc' => time()));
	} elseif($submit) {
		if($replying_to) {
			$new_info = Posts::make($replying_to, $author, $body);
			$new_topic_id = $new_info['topic'];
			$new_post_id = $new_info['id'];
		} else {
			$new_info = Topics::make($title, $author, $body);
			$new_topic_id = $new_info['id'];
			$new_post_id = $new_info['post'];
		}
		header('HTTP/1.1 303 See Other');
		Page::redirect(Topics::makeURI($new_topic_id, $new_post_id));
		return;
	}
}

$Page->title .= ' ' . ($replying_to ? 'Thread' : 'Topic');
Page::displayPostForm(($replying_to ? Page::FORM_THREAD : Page::FORM_TOPIC));