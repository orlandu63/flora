<?php
require '_.phps';
require 'markdown.php';
$Page->title = 'Post';
$thread = filter_input(INPUT_GET, 'thread', FILTER_SANITIZE_NUMBER_INT);
$submit = (bool)filter_input(INPUT_POST, 'submit');
$preview = (bool)filter_input(INPUT_POST, 'preview');
try {
	if(Posts::isFlooding()) {
		throw new Exception('You can only post once every 10 seconds.');
	}
	if($thread && !Posts::exists($thread)) {
		throw new Exception('Post does not exist.');
	}

	if($submit || $preview) {
		$author = trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS));
		if(Input::getUserAuthor() !== $author) {
			setcookie('author', $author); #FIX COOKIE ISSUE D:
		}
		if(strlen($author) > Posts::MAX_AUTHOR_LENGTH) {
			throw new Exception('Name must be no more than ' . Posts::MAX_AUTHOR_LENGTH . ' characters');
		}
		$body = trim(filter_input(INPUT_POST, 'body'));
		if(!$body) {
			throw new Exception('Body must be at least 1 char');
		}
		$body = Markdown($body);
		if(strlen($body) > Posts::MAX_BODY_LENGTH) {
			throw new Exception('Body must be no more than ' . Posts::MAX_BODY_LENGTH . ' characters.');
		}
		if(!$thread) {
			$title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
			if(!$title) {
				throw new Exception('Please input a title.');
			}
			if(strlen($title) > Topics::MAX_TITLE_LENGTH) {
				throw new Exception('Title must be no more than ' . Topics::MAX_TITLE_LENGTH . ' characters.');
			}
		}
	}
	
	if($thread) {
		echo '<h3>Replying to:</h3>';
		Posts::displayPost($thread);
	}

	if($preview) {
		echo '<h3>Preview: ';
		if(!$thread) {
			echo ' ', $title;
		}
		echo '</h3>';
		Posts::displayPost(array('body' => $body, 'author' => $author, 'toc' => time()));
	} elseif($submit) {
		if($thread) {
			$new_info = Posts::make($thread, $author, $body);
		} else {
			$new_info = Topics::make($title, $author, $body);
		}
		header('HTTP/1.1 303 See Other');
		header('Location: topic.php?id=' . $new_info['topic']);
		die;
	}
} catch(Exception $exception) {
	echo '<p>',  $exception->getMessage(), '<br/>Note that &lt;, &gt;, &amp; and " are considered as 4, 4, 5, ?? characters in web form.</p>';
	return;
}

Input::showContentCreationForm(($thread ? Input::FORM_THREAD : Input::FORM_TOPIC));