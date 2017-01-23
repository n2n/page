<?php
namespace page\model;

use rocket\spec\ei\component\field\impl\string\cke\model\CkeLinkProvider;
use n2n\context\RequestScoped;
use rocket\spec\ei\component\field\impl\string\cke\model\CkeLinkProviderAdapter;
use n2n\l10n\N2nLocale;

class PageCkeLinkProvider extends CkeLinkProviderAdapter implements RequestScoped {
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\impl\string\cke\model\CkeLinkProvider::getTitle()
	 */
	public function getTitle(): string {
		return 'Page Links';
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\impl\string\cke\model\CkeLinkProvider::getLinkUrls()
	 */
	public function getLinkOptions(N2nLocale $n2nLocale): array {
		return array('https://www.holeradio.ch' => 'Holeradio');
	}

// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\component\field\impl\string\cke\model\CkeLinkProvider::buildUrl()
// 	 */
// 	public function buildUrl(string $key) {
// 		return null;
// 	}
}