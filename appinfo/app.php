<?php
\OC::$server->getNavigationManager()->add(function () {
	$urlGenerator = \OC::$server->getURLGenerator();
	return [
		'id' => 'board',
		'order' => -1,
		'href' => $urlGenerator->linkToRoute('board_start'),
		'icon' => $urlGenerator->imagePath('activity', 'activity.svg'),
		'name' => \OC::$server->getL10N('board')->t('Forum')
	];
});
