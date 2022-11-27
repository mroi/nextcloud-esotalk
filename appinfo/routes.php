<?php
namespace OCA\Board\Controller;


$routes = ['routes' => [
	['name' => 'iframe#index', 'url' => '/'],
	['name' => 'iframe#content', 'url' => '/{path}', 'requirements' => array('path' => '.+')]
]];


class IframeController extends \OCP\AppFramework\Controller {
	private $urlGenerator;

	public function __construct(string $appName, \OCP\IRequest $request, \OC\URLGenerator $generator) {
		parent::__construct($appName, $request);
		$this->urlGenerator = $generator;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		$link_index = $this->urlGenerator->linkToRoute('board.iframe.index');
		$link_content = $this->urlGenerator->linkToRoute('board.iframe.content', ['path' => 'conversations']);

		$iframe = <<<IFRAME
			<div style="width:100%;overflow:auto;-webkit-overflow-scrolling:touch;"><iframe id="iframe" src="$link_content" style="width:100%;height:100%;margin-bottom:-6px;"></iframe></div>
			<script type="text/javascript">
				var iframe = document.getElementById('iframe');
				var basepath = "$link_index";
				if (window.location.hash) {
					iframe.src = basepath + window.location.hash.substring(1);
				}
				iframe.onload = function() {
					var path = iframe.contentWindow.location.pathname;
					window.location.hash = '#' + path.replace(basepath, '');
				};
			</script>
		IFRAME;

		$response = new HTMLResponse($iframe);

		$csp = new \OCP\AppFramework\Http\ContentSecurityPolicy();
		$csp->addAllowedScriptDomain('\'sha256-SAnhMxMi7x7OPtXfG13eeBPVjj1/s0cYm+asG1Keejk=\'');
		$response->setContentSecurityPolicy($csp);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function content(string $path) {
		return new BoardResponse($path);
	}
}


class HTMLResponse extends \OCP\AppFramework\Http\TemplateResponse {
	private $content;

	public function __construct(string $content) {
		parent::__construct(\OC::$REQUESTEDAPP, '');
		$this->content = $content;
	}

	public function render() {
		$template = new InlineTemplate($this->content, $this->getRenderAs());
		return $template->fetchPage($this->getParams());
	}
}


// use a custom template subclass to avoid the need for a separate template file
// attention: this uses Nextcloud private APIs
class InlineTemplate extends \OCP\Template {
	private $content;

	public function __construct(string $content, string $renderAs) {
		parent::__construct(\OC::$REQUESTEDAPP, '', $renderAs);
		$this->content = $content;
	}

	protected function findTemplate($theme, $app, $name) {
		return array('', '');
	}
	protected function load($file, $params = null) {
		return $this->content;
	}
}


class BoardResponse extends \OCP\AppFramework\Http\TemplateResponse {
	private $path;

	public function __construct(string $path) {
		parent::__construct(\OC::$REQUESTEDAPP, '');
		$this->path = $path;
	}

	public function render() {
		$csp = new \OCP\AppFramework\Http\ContentSecurityPolicy();
		$csp->allowInlineScript(true);
		$csp->addAllowedChildSrcDomain('\'self\'');
		$csp->addAllowedChildSrcDomain('www.youtube-nocookie.com');
		$csp->addAllowedChildSrcDomain('player.vimeo.com');
		$this->setContentSecurityPolicy($csp);

		foreach ($this->getHeaders() as $name => $value) {
			header($name . ': ' . $value);
		}

		$_GET['p'] = $this->path;
		require(__DIR__ . '/../index.php');

		exit();
	}
}


return $routes;
