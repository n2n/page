<?php
namespace page\rocket\ei\field\conf;

use n2n\web\dispatch\mag\impl\model\MagForm;
use n2n\core\container\N2nContext;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\web\dispatch\mag\MagCollection;
use rocket\spec\ei\component\EiSetupProcess;
use page\config\PageConfig;
use n2n\reflection\CastUtils;
use page\rocket\ei\field\PageSubsystemEiField;
use rocket\spec\ei\component\field\impl\adapter\AdaptableEiFieldConfigurator;
use n2n\l10n\DynamicTextCollection;
use rocket\spec\ei\manage\gui\DisplayDefinition;

class PageSubsystemEiFieldConfigurator extends AdaptableEiFieldConfigurator {
	private $pageSubsystemEiField;
	
	public function __construct(PageSubsystemEiField $pageSubsystemEiField) {
		parent::__construct($pageSubsystemEiField);
		$this->autoRegister();
		
		$this->pageSubsystemEiField = $pageSubsystemEiField;
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		$n2nContext = $eiSetupProcess->getN2nContext();
		$pageConfig = $eiSetupProcess->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		$dtc = new DynamicTextCollection('page', $n2nContext->getN2nLocale());
		$subsystems = $eiSetupProcess->getN2nContext()->getHttpContext()->getAvailableSubsystems();

		if (empty($subsystems)) {
			$this->pageSubsystemEiField->setDisplayDefinition(new DisplayDefinition(DisplayDefinition::NO_VIEW_MODES));
		}
		
		$options = array(null => $dtc->translate('all_subsystems_label'));
		foreach ($subsystems as $subsystem) {
			$displayName = $subsystem->getName() . ' (' . $subsystem->getHostName();
			if (null !== ($contextPath = $subsystem->getContextPath())) {
				$displayName .= '/' . $subsystem->getContextPath();
			}
			$displayName .= ')';
			
			$options[$subsystem->getName()] = $displayName;
		}
		
		
		$this->pageSubsystemEiField->setOptions($options);
		
	}
	
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable {
		return new MagForm(new MagCollection());
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext) {
	
	}
}