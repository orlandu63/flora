<?php
require '_.phps';
require 'classes/topiclist.phps';

$Page->title = 'Home';
$page_number = max(0, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT));
$Topiclist = new Topiclist($page_number);
$Topiclist->render();
?>
<hr/>
<?php
Input::showContentCreationForm(Input::FORM_TOPIC);