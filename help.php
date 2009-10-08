<?php
require '_.phps';
$Page->page_id = Page::PAGE_HELP;
$Page->title = $Page->header = 'Help';

$Page->load('markdown_help');