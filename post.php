<?php
require '_.phps';
require 'markdown.phps';

$Page->page_id = Page::PAGE_POST;
$Page->title = $Page->header = 'Post';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$replying_to = InputValidation::validateInt('post', 1, Posts::max());
$post_exists = ($replying_to ? Posts::exists($replying_to) : null);
$making_topic = !$replying_to;

$submit = (bool)filter_input(INPUT_POST, 'submit');
$preview = (bool)filter_input(INPUT_POST, 'preview');

if($replying_to && $post_exists) {
	$post_info = Posts::getInfo($replying_to);
	$topic_info = Topics::getInfo($post_info['topic']);
	$Page->page_id .= $replying_to;
	$Page->site_nav['Back to Post'] = Topics::makeURI($topic_info['id'], $replying_to);
	echo '<h3>Replying to: <a href="', Topics::makeURI($topic_info['id'], $topic_info['post']), '">',
		$topic_info['title'],
	'</a></h3>';
	Posts::display($post_info);
}

try {
	if(User::isFlooding()) {
		throw new Exception(sprintf('You can only post once every %d seconds.', (1 / Posts::POSTS_PER_SECOND)));
	}

	if($replying_to && !$post_exists) {
		throw new InvalidArgumentException('Post does not exist.');
	}

	if($submit || $preview) {
		$author = InputValidation::validateAuthor();
		User::$name = $author;
		$body = InputValidation::validateBody();
		if($making_topic) {
			$title = InputValidation::validateTitle();
		}
	}
	
	$valid = true;
} catch(Exception $exception) {
	Page::error($exception->getMessage(), 400);
	$valid = false;
}

if($valid) {
	if($preview) {
		echo '<h3>Preview:';
		if($making_topic) {
			echo ' ', $title;
		}
		echo '</h3>';
		Posts::display(array('body' => $body, 'author' => $author, 'toc' => $_SERVER['REQUEST_TIME']));
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
		Page::redirect(BASE_PATH . Topics::makeURI($new_topic_id, $new_post_id), 303);
	}
}

$Page->title .= ' ' . ($replying_to ? 'Thread' : 'Topic');
$Page->displayPostForm(($replying_to ? Page::FORM_THREAD : Page::FORM_TOPIC));