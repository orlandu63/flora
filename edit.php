<?php
return;
require '_.phps';

#$Page->page_id = Page::PAGE_EDIT;
$Page->title = $Page->header = 'Edit';
$Page->site_nav['Topic Index'] = Page::makeURI(Page::PAGE_INDEX);

$post = InputValidation::validateInt('post', 1, Posts::max());
if(!Posts::exists($post)) {
	Page::error('Invalid post ID', 404);
	return;
}

$post_info = array('body' => Posts::getVanillaText($post)) + Posts::getInfo($post);

$Page->displayPostForm(Page::FORM_THREAD, $post_info + array('post' => $post));

$Page->page_id .= $post;
$Page->site_nav['Back to Post'] = Topics::makeURI($post_info['topic'], $post);