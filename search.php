<?php
require '_.phps';
load_class('topiclist');

$Page->page_id = Page::PAGE_SEARCH;
$Page->header = 'Search';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$Page->load('forms/search', 
	array('query' => filter_input(INPUT_POST, 'query', FILTER_SANITIZE_SPECIAL_CHARS))
);

if(!filter_has_var(INPUT_POST, 'submit')) {
	return;
}

try {
	$query = InputValidation::validateTitle(filter_input(INPUT_POST, 'query'), 'query');
} catch(Exception $exception) {
	Page::error($exception->getMessage(), 400);
	return;
}

$Page->header .= ': ' . $query;

$search_results = Topics::search($query);
if(empty($search_results)) {
	Page::error('No results.', 404);
} else {
	$Topiclist = new Topiclist($search_results);
	$Topiclist->render();
	$Topiclist->renderPagination(0, count($search_results));
}