<?php
require '_.phps';

$Page->id(Page::PAGE_POST);
$Page->title = $Page->header = 'Post';

$replying_to = InputValidation::validateInt('post', 1, Posts::max());
$making_topic = !$replying_to;

$submit = filter_has_var(INPUT_POST, 'submit');
$preview = filter_has_var(INPUT_POST, 'preview');
$form_submitted = $submit || $preview;

if($replying_to && Posts::exists($replying_to)) {
	$post_info = Posts::getInfo($replying_to);
	$topic_info = Topics::getInfo($post_info['topic']);
	$Page->id($replying_to);
	$Page->site_nav['Back to Post'] = Topics::makeURI($topic_info['id'], $replying_to);
	echo '<h3>Replying to: <a href="', Topics::makeURI($topic_info['id'], $topic_info['post']), '">',
		$topic_info['title'],
	'</a></h3>';
	Posts::display($post_info);
}

try {
	if($form_submitted && User::isFlooding()) {
		throw new Exception(sprintf('You can only post once every %d seconds.', (1 / Posts::POSTS_PER_SECOND)));
	}

	if($replying_to && !Posts::exists($replying_to)) {
		throw new InvalidArgumentException('Post does not exist.');
	}

	if($form_submitted) {
		$author = InputValidation::validateAuthor('author');
		User::$name = $author;
		$body = InputValidation::validateBody('body');
		if($making_topic) {
			$title = InputValidation::validateTitle('title');
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
		Posts::display(
			array('body' => $body, 'author' => $author, 'toc' => $_SERVER['REQUEST_TIME'], 'user_id' => User::$id)
		);
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
		HTTP::redirect(Settings::get('BASE_PATH') . Topics::makeURI($new_topic_id, $new_post_id), 303);
		$Page->terminate();
	}
}

$Page->title .= ' ' . ($replying_to ? 'Thread' : 'Topic');
$Page->load(Form::preparePostForm(($replying_to ? Form::POST_THREAD : Form::POST_TOPIC)));