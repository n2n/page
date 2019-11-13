<?php
namespace page\rocket\ei\field\conf;

use n2n\impl\web\dispatch\mag\model\MagForm;
use n2n\web\dispatch\mag\MagCollection;
use page\config\PageConfig;
use n2n\util\type\CastUtils;
use page\rocket\ei\field\PageSubsystemEiProp;
use n2n\l10n\DynamicTextCollection;
use rocket\ei\manage\gui\ViewMode;
use rocket\impl\ei\component\prop\adapter\config\DisplayConfig;
use rocket\impl\ei\component\prop\adapter\config\ConfigAdaption;
use rocket\ei\util\Eiu;
use n2n\util\type\attrs\DataSet;
use rocket\impl\ei\component\prop\enum\conf\EnumConfig;

class PageSubsystemConfig extends ConfigAdaption {
	private $pageSubsystemEiField;
	private $displayConfig;
	private $enumConfig;
	
	public function __construct(PageSubsystemEiProp $pageSubsystemEiField, DisplayConfig $displayConfig,
			EnumConfig $enumConfig) {
		$this->pageSubsystemEiField = $pageSubsystemEiField;
		$this->displayConfig = $displayConfig;
		$this->enumConfig = $enumConfig;
	}
	
	public function setup(Eiu $eiu, DataSet $dataSet) {
		$n2nContext = $eiu->getN2nContext();
		$pageConfig = $eiu->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		$dtc = new DynamicTextCollection('page', $n2nContext->getN2nLocale());
		$subsystems = $eiu->getN2nContext()->getHttpContext()->getAvailableSubsystems();

		if (empty($subsystems)) {
			$this->displayConfig->setCompatibleViewModes(ViewMode::none());
// 			$this->pageSubsystemEiField->setDisplayConfig(new DisplayConfig(ViewMode::none()));
		}
		
		$options = array(null => $dtc->translate('all_subsystems_label'));
		foreach ($subsystems as $subsystem) {
			$displayName = $subsystem->getName() . ' (' . $subsystem->getHostName();
			if (null !== ($contextPath = $subsystem->getContextPath())) {
				$displayName .= '/' . $contextPath;
			}
			$displayName .= ')';
			
			$options[$subsystem->getName()] = $displayName;
		}
		
		
		$this->enumConfig->setOptions($options);
		
	}
	
	public function mag(Eiu $eiu, DataSet $dataSet, MagCollection $magCollection) {
		return new MagForm(new MagCollection());
	}
	
	public function save(Eiu $eiu, MagCollection $magCollection, DataSet $dataSet) {
	
	}
}