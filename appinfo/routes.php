<?php
$this->create('board_start', '')
	->actionInclude('board/appinfo/iframe.php');
// esoTalk uses the p parameter to determine the request path
	->actionInclude('board/index.php');
$this->create('board_content', '{p}')
	->requirements(array('p' => '.+'))
