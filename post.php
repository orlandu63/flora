<?php
require '_.phps';
require CLASS_DIR . 'inputvalidation.phps';
require 'markdown.phps';

$Page->page_id = Page::PAGE_POST;
$Page->title = $Page->header = 'Post';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$replying_to = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT);
$making_topic = !$replying_to;
$submit = (bool)filter_input(INPUT_POST, 'submit');
$preview = (bool)filter_input(INPUT_POST, 'preview');

if($replying_to && Posts::exists($replying_to)) {
	$topic_info = Topics::getInfo(Posts::getTopicById($replying_to));
	$Page->page_id .= $replying_to;
	$Page->site_nav['Back to Post'] = Topics::makeURI($topic_info['id'], $replying_to);
	echo '<h3>Replying to: <a href="', Topics::makeURI($topic_info['id'], $topic_info['post']), '">',
		$topic_info['title'],
	'</a></h3>';
	Posts::display($replying_to);
}

try {
	if(User::isFlooding()) {
		throw new Exception('You can only post once every 10 seconds.');
	}

	if($replying_to && !Posts::exists($replying_to)) {
		throw new InvalidArgumentException('Post does not exist.');
	}

	if($submit || $preview) {
		$author = InputValidation::validate(InputValidation::VALIDATE_AUTHOR);
		User::$name = $author;
		$body = InputValidation::validate(InputValidation::VALIDATE_BODY);
		if($making_topic) {
			$title = InputValidation::validate(InputValidation::VALIDATE_TITLE);
		}
	}
	
	$valid = true;
} catch(Exception $exception) {
	Page::error($exception->getMessage());
	$valid = false;
}

if($valid) {
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
	}
}

$Page->title .= ' ' . ($replying_to ? 'Thread' : 'Topic');
Page::displayPostForm(($replying_to ? Page::FORM_THREAD : Page::FORM_TOPIC));