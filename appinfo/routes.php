<?php
$this->create('board_iframe', '')
	->actionInclude('board/appinfo/iframe.php');
// esoTalk uses the p parameter to determine the request path
$this->create('board_index', '{p}')
	->requirements(array('p' => '.*'))
	->actionInclude('board/index.php');
