<?php
OCP\User::checkLoggedIn();
\OC::$server->getNavigationManager()->setActiveEntry(\OC::$REQUESTEDAPP);

// check if we should generate the iframe wrapper page or the iframe content
if (!empty($_GET['p'])) {
	// request includes path parameter, generate iframe content
	require(__DIR__ . '/../index.php');
	return;
}

// use a custom template subclass to avoid the need for an extra templates subdirectory
// attention: this uses Nextcloud private APIs
class SimpleTemplate extends OCP\Template {
	private $content;
	public function __construct($app, $content) {
		$this->content = $content;
		parent::__construct($app, $app, 'user');
	}
	protected function findTemplate($theme, $app, $name) {
		if ($name !== $app) return parent::findTemplate($theme, $app, $name);
		return array('', $app);
	}
	protected function load($file, $params = null) {
		if ($file !== $this->app) return parent::load($file, $params);
		return $this->content;
	}
}

ob_start();
?>
	<div style="width:100%;position:absolute;top:50px;bottom:0;overflow:auto;-webkit-overflow-scrolling:touch;"><iframe src="<?php echo \OC::$server->getURLGenerator()->linkToRoute('board_content', array('p' => 'conversations')) /* TODO: hardcoded default route */ ?>" style="width:100%;height:100%;margin-bottom:-6px;"></iframe></div>
<?php
$iframe = ob_get_contents();
@ob_end_clean();

$template = new SimpleTemplate(\OC::$REQUESTEDAPP, $iframe);
$template->printPage();
