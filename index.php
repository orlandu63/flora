<?php
require '_.phps';
load_class('topiclist');

$Page->page_id = Page::PAGE_INDEX;
$Page->site_nav['Home'] = '/';
$Page->site_nav['Create a Topic'] = Page::makeURI(Page::PAGE_POST);
$Page->header = 'Topic Index';

$page_number = InputValidation::validateInt('page', 0, Topics::max() / Topiclist::PER_PAGE);
if($page_number === false) {
	Page::error('Invalid page number', 400);
	return;
}

if($page_number > 0) {
	$Page->header .= sprintf(', page %d', $page_number + 1);
}
$Page->page_id .= $page_number;

$Topiclist = new Topiclist($page_number);
$Topiclist->render(Topiclist::WITH_PAGINATION);

$Page->displayPostForm(Page::FORM_TOPIC);