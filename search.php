<?php
require '_.phps';
load_class('topiclist');

$Page->page_id = Page::PAGE_SEARCH;
$Page->header = 'Search';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$query = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_SPECIAL_CHARS);
$Page->load('forms/search', array('query' => $query));

if(!filter_has_var(INPUT_POST, 'submit')) {
	return;
}

try {
	InputValidation::validateLength('query', $query, Topics::MAX_TITLE_LENGTH);
} catch(Exception $exception) {
	Page::error($exception->getMessage(), 400);
	return;
}

$Page->header .= ': ' . $query;

$search_results = Topics::search($query);
if(empty($search_results)) {
	Page::error('No results.', 404);
} else {
	echo '<h2>Results</h2>';
	$Topiclist = new Topiclist($search_results);
	$Topiclist->render();
}