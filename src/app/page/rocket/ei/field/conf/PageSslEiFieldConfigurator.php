<?php
namespace page\rocket\ei\field\conf;

use rocket\spec\ei\component\field\impl\adapter\AdaptableEiFieldConfigurator;
use rocket\spec\ei\component\EiSetupProcess;
use page\config\PageConfig;
use n2n\reflection\CastUtils;
use rocket\spec\ei\manage\gui\DisplayDefinition;
use n2n\core\container\N2nContext;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\web\dispatch\mag\impl\model\MagForm;
use n2n\web\dispatch\mag\MagCollection;
use page\rocket\ei\field\PageSslEiField;

class PageSslEiFieldConfigurator extends AdaptableEiFieldConfigurator {
	private $pageSslEiField;
	
	public function __construct(PageSslEiField $pageSslEiField) {
		parent::__construct($pageSslEiField);
		$this->pageSslEiField = $pageSslEiField;
		$this->autoRegister();
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		$pageConfig = $eiSetupProcess->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		if (!$pageConfig->isSslSelectable()) {
			$this->pageSslEiField->setDisplayDefinition(new DisplayDefinition(DisplayDefinition::NO_VIEW_MODES));
		}
	}
	
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable {
		return new MagForm(new MagCollection());
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext) {
	}
}