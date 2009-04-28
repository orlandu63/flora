<?php
require '_.phps';
require CLASS_DIR . 'threadlist.phps';

$Page->pageId = Page::PAGE_TOPIC;
$Page->title = 'Topic';
$topic = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!Topics::exists($topic)) {
	Page::error('Invalid topic ID');
	return;
}

$Threadlist = new ThreadList($topic);
	$Page->title = $Threadlist->topic['title'];
	echo '<h2><a href="', Topics::makeURI($Threadlist->topic['id']), '">',
		$Threadlist->topic['title'],
	'</a></h2>';
$Threadlist->render();