<?php
require '_.phps';
require CLASS_DIR . 'topiclist.phps';

$Page->page_id = Page::PAGE_INDEX;
$Page->site_nav['Home'] = '/';
$Page->site_nav['Create a Topic'] = Page::makeURI(Page::PAGE_POST);

$page_number = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if($page_number < 0) {
	Page::error('Invalid page number');
	return;
}

$Page->header = 'Topic Index';
$Page->page_id .= $page_number;

$Topiclist = new Topiclist($page_number);
$Topiclist->render(Topiclist::WITH_PAGINATION);

Page::displayPostForm(Page::FORM_TOPIC);