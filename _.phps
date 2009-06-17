<?php
define('VERSION', '1.0T');
define('CLASS_DIR', 'classes/');

//these are located inside the include path
require 'db.phps';
require 'stemplator.phps';

require CLASS_DIR . 'user.phps';
require CLASS_DIR . 'page.phps';
require CLASS_DIR . 'posts.phps';
require CLASS_DIR . 'topics.phps';

$Page = new Page;
$DB = new DB('flora');
User::refresh();