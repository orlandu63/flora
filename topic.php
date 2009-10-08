<?php
require '_.phps';
load_class('threadlist');

$Page->page_id = Page::PAGE_TOPIC;
$Page->title = $Page->header = 'Topic';

$topic = InputValidation::validateInt('id', 1, Topics::max());
if(!Topics::exists($topic)) {
	Page::error('Invalid topic ID', 404);
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