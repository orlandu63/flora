<?php
require '_.phps';
require CLASS_DIR . 'threadlist.phps';

$Page->pageId = Page::PAGE_TOPIC;
$Page->title = 'Topic';
$topic = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!Topics::exists($topic)) {
	return;
}

$Threadlist = new ThreadList($topic);
	echo '<h1>', ($Page->title = $Threadlist->topic['title']), '</h1>';
$Threadlist->render();