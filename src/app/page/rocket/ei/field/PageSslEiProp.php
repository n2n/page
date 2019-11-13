<?php
namespace page\rocket\ei\field;

use rocket\impl\ei\component\prop\bool\BooleanEiProp;
use rocket\ei\util\Eiu;
use page\config\PageConfig;
use n2n\util\type\CastUtils;
use rocket\ei\manage\gui\ViewMode;

class PageSslEiProp extends BooleanEiProp {
	
	function getTypeName(): string {
		return 'Ssl ScriptField (Page)';
	}
	
	function prepare() {
		$this->getConfigurator()->addSetupCallback(function (Eiu $eiu) {
			$pageConfig = $eiu->getN2nContext()->getModuleConfig('page');
			CastUtils::assertTrue($pageConfig instanceof PageConfig);
			
			if (!$pageConfig->isSslSelectable()) {
				$this->getDisplayConfig()->setCompatibleViewModes(ViewMode::none());
			}
		});
	}
}
