<?php
require '_.phps';
Loader::load('Threadlist');

$Page->id(Page::PAGE_TOPIC);
$Page->title = $Page->header = 'Topic';

$topic = InputValidation::validateInt('id', 1, Topics::max());
if(!Topics::exists($topic)) {
	Page::error('Invalid topic ID', 404);
	return;
}

$Page->id($topic);

$topic_info = Topics::getInfo($topic);
$Page->title = $topic_info['title'];
$Page->header = sprintf(
	'<a href="%s">%s</a>', Topics::makeURI($topic_info['id'], $topic_info['post']), $topic_info['title']
);

$Threadlist = new ThreadList($topic);
$Page->load($Threadlist);