<?php
define('VERSION', '0.6');
define('CLASS_DIR', 'classes/');

require 'showsource.phps';
require 'utilities.php';
require 'db.php';
require 'stemplator.php';
require CLASS_DIR . 'user.phps';
require CLASS_DIR . 'page.phps';
require CLASS_DIR . 'posts.phps';
require CLASS_DIR . 'topics.phps';

$DB = new DB('flora');
$Page = new Page;
User::refresh();