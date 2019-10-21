<?php
$this->create('board_start', '')
	->actionInclude('board/appinfo/iframe.php');
// esoTalk uses the p parameter to determine the request path
$this->create('board_content', '{p}')
	->requirements(array('p' => '.+'))
	->actionInclude('board/appinfo/iframe.php');
