<?php
require '_.phps';
load_class('topiclist');

$Page->id(Page::PAGE_INDEX);
$Page->header = 'Topic Index';
$Page->site_nav['Create a Topic'] = Page::makeURI(Page::PAGE_POST);
$Page->site_nav['Search Topics'] = Page::makeURI(Page::PAGE_SEARCH);
$Page->site_nav['Help'] = Page::makeURI(Page::PAGE_HELP);

$page = InputValidation::validateInt('page', 0, Topiclist::getNumPages(Topics::count()));
if($page === false) {
	Page::error('Invalid page number', 400);
	return;
}

if($page > 0) {
	$Page->header .= sprintf(', page %d', $page + 1);
}
$Page->id($page);

$Topiclist = new Topiclist(Topics::getList($page, Settings::get('topiclist/per_page')));
$Page->load($Topiclist);
$Topiclist->renderPagination($page, Topics::count());

$Page->load(Form::preparePostForm(Form::POST_TOPIC));