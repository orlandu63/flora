<?php
require '_.phps';
require CLASS_DIR . 'threadlist.phps';

$Page->page_id = Page::PAGE_TOPIC;
$Page->title = 'Topic';
$topic = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$newness_threshold = strtotime(filter_input(INPUT_POST, 'newness_threshold'));

if(!Topics::exists($topic)) {
	Page::error('Invalid topic ID');
	return;
}

$Page->page_id .= $topic;

$Threadlist = new ThreadList($topic, $newness_threshold);
	$Page->title = $Threadlist->topic['title'];
	$Page->header = sprintf(
		'<a href="%s">%s</a>',
		Topics::makeURI($Threadlist->topic['id'], $Threadlist->topic['post']),
		$Threadlist->topic['title']
	);
$Threadlist->render();