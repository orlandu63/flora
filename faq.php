<?php
require '_.phps';
$Page->page_id = 'faq';
$Page->title = $Page->header = 'FAQ';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$Page->load('markdown_help');