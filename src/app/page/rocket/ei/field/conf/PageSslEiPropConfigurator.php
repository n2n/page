<?php
namespace page\rocket\ei\field\conf;

use rocket\impl\ei\component\prop\adapter\AdaptableEiPropConfigurator;
use rocket\spec\ei\component\EiSetupProcess;
use page\config\PageConfig;
use n2n\reflection\CastUtils;
use n2n\core\container\N2nContext;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\impl\web\dispatch\mag\model\MagForm;
use n2n\web\dispatch\mag\MagCollection;
use page\rocket\ei\field\PageSslEiProp;
use rocket\impl\ei\component\prop\adapter\DisplaySettings;
use rocket\spec\ei\manage\gui\ViewMode;

class PageSslEiPropConfigurator extends AdaptableEiPropConfigurator {
	private $pageSslEiField;
	
	public function __construct(PageSslEiProp $pageSslEiField) {
		parent::__construct($pageSslEiField);
		$this->pageSslEiField = $pageSslEiField;
		$this->autoRegister();
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		$pageConfig = $eiSetupProcess->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		if (!$pageConfig->isSslSelectable()) {
			$this->pageSslEiField->setDisplaySettings(new DisplaySettings(ViewMode::none()));
		}
	}
	
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable {
		return new MagForm(new MagCollection());
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext) {
	}
}