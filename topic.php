<?php
require '_.phps';
require 'classes/threadlist.phps';

$Page->title = 'Topic';
$topic = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!Topics::exists($topic)) {
	return;
}

$Threadlist = new ThreadList($topic);
$Page->title = $Threadlist->topic_info['title'];
$Threadlist->render();