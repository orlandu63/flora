<?php
return array(
	'software' => 'flora',
	'base_path' => sprintf('http://%s/%s/', $_SERVER['HTTP_HOST'], 'uamb'),
	'db_name' => 'flora',
	'default_template_vars' => array(
		'forum_name' => 'UAMB',
		'announcement' => 'welcome to UAMB, an <del>unmoderated anonymous</del><ins>uber awesome</ins> message board.',
	),
	'date_format' => 'r',
	'admin_ids' => array('63dc48'),
	'user' => array(
		'anon_name' => 'anon',
		'id_length' => 6,
	),
	'input_thresholds' => array(
		'body' => array(
			'max_length' => 8000,
			'min_length' => 1,
		),
		'title' => array(
			'max_length' => 80,
			'min_length' => 1,
		),
		'author' => array(
			'max_length' => 10,
			'min_length' => 0,
		),
		'search' => array(
			'min_word_length' => 3,
		),
		'posts_per_second' => .1,
	),
	'topiclist' => array(
		'per_page' => 30,
	),
);