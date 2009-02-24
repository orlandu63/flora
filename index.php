<?php
require '_.php';
require 'classes/topiclist.phps';

$Page->title = 'Topic Index';
$page_number = max(0, filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT));
$Topiclist = new Topiclist($page_number);
$Topiclist->display();
$Topiclist->pagination();
?>
<hr/>
<?php
Input::showContentCreationForm(Input::FORM_TOPIC);