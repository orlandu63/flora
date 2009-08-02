<?php
require '_.phps';
load_class('topiclist');

$Page->page_id = Page::PAGE_INDEX;
$Page->header = 'Topic Index';
$Page->site_nav['Home'] = '/';
$Page->site_nav['Create a Topic'] = Page::makeURI(Page::PAGE_POST);
$Page->site_nav['Search Topics'] = Page::makeURI(Page::PAGE_SEARCH);

$page = InputValidation::validateInt('page', 0, Topiclist::getNumPages(Topics::count()));
if($page === false) {
	Page::error('Invalid page number', 400);
	return;
}

if($page > 0) {
	$Page->header .= sprintf(', page %d', $page + 1);
}
$Page->page_id .= $page;

$Topiclist = new Topiclist(Topics::getList($page, Topiclist::PER_PAGE));
$Topiclist->render();
$Topiclist->renderPagination($page, Topics::count());

$Page->displayPostForm(Page::FORM_TOPIC);