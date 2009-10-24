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

$topic_info = Topics::getInfo($topic);
$Threadlist = new ThreadList($topic);

$Page->title = $topic_info['title'];
$Page->header = sprintf(
	'<a href="%s">%s</a>', Topics::makeURI($topic_info['id'], $topic_info['post']), $topic_info['title']
);

$Page->load($Threadlist);