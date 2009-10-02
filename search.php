<?php
require '_.phps';
load_class('topiclist');

$Page->page_id = Page::PAGE_SEARCH;
$Page->header = 'Search';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$Page->load(
	new Form(Form::SEARCH, array('query' => 
		InputValidation::filter_input(INPUT_POST, 'query', FILTER_SANITIZE_SPECIAL_CHARS),
	))
);

if(!filter_has_var(INPUT_POST, 'submit')) {
	return;
}

try {
	$query = InputValidation::validateQuery('query');
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