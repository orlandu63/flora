<?php
require '_.phps';
require CLASS_DIR . 'threadlist.phps';

$Page->page_id = Page::PAGE_TOPIC;
$Page->title = 'Topic';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$topic = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!Topics::exists($topic)) {
	Page::error('Invalid topic ID');
	return;
}

$Page->page_id .= $topic;

$Threadlist = new ThreadList($topic);
	$Page->title = $Threadlist->topic['title'];
	$Page->header = sprintf(
		'<a href="%s">%s</a>',
		Topics::makeURI($Threadlist->topic['id'], $Threadlist->topic['post']),
		$Threadlist->topic['title']
	);
$Threadlist->render();