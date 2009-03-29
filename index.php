<?php
require '_.phps';
require CLASS_DIR . 'topiclist.phps';

$page_number = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if($page_number < 0) {
	return;
}
$Topiclist = new Topiclist($page_number);
$Topiclist->render();

Page::showContentCreationForm(Page::FORM_TOPIC);