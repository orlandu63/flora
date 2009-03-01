<?php
require '_.phps';
require 'markdown.php';
$Page->title = 'Post';
$thread = filter_input(INPUT_GET, 'thread', FILTER_SANITIZE_NUMBER_INT);
$submit = (bool)filter_input(INPUT_POST, 'submit');
$preview = (bool)filter_input(INPUT_POST, 'preview');
try {
	if(User::isFlooding()) {
		throw new Exception('You can only post once every 10 seconds.');
	}
	if($thread && !Post::exists($thread)) {
		throw new Exception('Post does not exist.');
	}

	if($submit || $preview) {
		$author = Input::validate(Input::VALIDATE_AUTHOR);
		$body = Input::validate(Input::VALIDATE_BODY);
		if(!$thread) {
			$title = Input::validate(INPUT::VALIDATE_TITLE);
		}
	}
} catch(Exception $exception) {
	echo '<p>',  $exception->getMessage(), '<br/>Note that &lt;, &gt;, &amp; and " are actually 4, 4, 5, ?? characters in web form.</p>';
	return;
}
	
if($thread) {
	echo '<h3>Replying to:</h3>';
	Post::display($thread);
}

if($preview) {
	echo '<h3>Preview:';
	if(!$thread) {
		echo ' ', $title;
	}
	echo '</h3>';
	Post::display(array('body' => $body, 'author' => $author, 'toc' => time()));
} elseif($submit) {
	if($thread) {
		$new_info = Post::make($thread, $author, $body);
	} else {
		$new_info = Topic::make($title, $author, $body);
	}
	header('HTTP/1.1 303 See Other');
	header('Location: topic.php?id=' . $new_info['topic']);
	die;
}

Input::showContentCreationForm(($thread ? Input::FORM_THREAD : Input::FORM_TOPIC));