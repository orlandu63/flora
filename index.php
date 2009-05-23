<?php
require '_.phps';
require CLASS_DIR . 'topiclist.phps';

$Page->pageID = Page::PAGE_INDEX;
$page_number = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if($page_number < 0) {
	Page::error('Invalid page number');
	return;
}

$Topiclist = new Topiclist($page_number);
$Topiclist->render(Topiclist::WITH_PAGINATION);

Page::displayPostForm(Page::FORM_TOPIC);