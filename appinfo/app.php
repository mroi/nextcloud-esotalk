<?php
OCP\App::addNavigationEntry(array(
	'id' => 'board',
	'order' => -1,
	'href' => OCP\Util::linkTo('board', 'appinfo/iframe.php'),
	'icon' => OCP\Util::imagePath('activity', 'activity.svg'),
	'name' => OCP\Util::getL10N('board')->t('Forum')
));
