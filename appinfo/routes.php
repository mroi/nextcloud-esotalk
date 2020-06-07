<?php
$this->create('board_start', '/apps/board/')
	->actionInclude('board/appinfo/iframe.php');
// esoTalk uses the p parameter to determine the request path
$this->create('board_content', '/apps/board/{p}')
	->requirements(array('p' => '.+'))
	->actionInclude('board/appinfo/iframe.php');
