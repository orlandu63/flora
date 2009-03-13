<?php
require '_.phps';
require 'classes/topiclist.phps';

$Page->title = 'Home';
$page_number = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
if($page_number < 0) {
	return;
}
$Topiclist = new Topiclist($page_number);
$Topiclist->render();

Input::showContentCreationForm(Input::FORM_TOPIC);