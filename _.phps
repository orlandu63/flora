<?php
define('VERSION', '0.7.1');
define('CLASS_DIR', 'classes/');

//these are located inside the include path
require 'db.php';
require 'stemplator.php';

require CLASS_DIR . 'user.phps';
require CLASS_DIR . 'page.phps';
require CLASS_DIR . 'posts.phps';
require CLASS_DIR . 'topics.phps';

$Page = new Page;
$DB = new DB('flora');
User::refresh();