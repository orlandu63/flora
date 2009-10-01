<?php
require '_.phps';
$Page->page_id = 'help';
$Page->title = $Page->header = 'Help';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$Page->load('markdown_help');