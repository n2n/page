<?php
//namespace page\rocket\ei\field\conf;
//
//use n2n\l10n\DynamicTextCollection;
//use n2n\util\type\CastUtils;
//use n2n\util\type\attrs\DataSet;
//use n2n\web\dispatch\mag\MagCollection;
//use page\config\PageConfig;
//use rocket\op\ei\manage\gui\ViewMode;
//use rocket\op\ei\util\Eiu;
//use rocket\impl\ei\component\prop\adapter\config\DisplayConfig;
//use rocket\impl\ei\component\prop\enum\conf\EnumConfig;
//
//
//class PageSubsystemConfig {
//
//	private $enumConfig;
//
//	function __construct(EnumConfig $enumConfig) {
//		$this->enumConfig = $enumConfig;
//	}
//
//	public function mag(Eiu $eiu, DataSet $dataSet, MagCollection $magCollection) {
//	}
//
//	public function save(Eiu $eiu, MagCollection $magCollection, DataSet $dataSet) {
//
//	}
//
//	public function setup(Eiu $eiu, DataSet $dataSet) {
//		$pageConfig = $eiu->getN2nContext()->getModuleConfig('page');
//		CastUtils::assertTrue($pageConfig instanceof PageConfig);
//
//		$dtc = new DynamicTextCollection('page', $eiu->getN2nLocale());
//		$subsystems = $eiu->getN2nContext()->getHttpContext()->getAvailableSubsystems();
//
//// 		if (empty($subsystems)) {
//// 			$this->pageSubsystemEiField->setDisplayConfig(new DisplayConfig(ViewMode::none()));
//// 		}
//
//		$options = array(null => $dtc->translate('all_subsystems_label'));
//		foreach ($subsystems as $subsystem) {
//			$displayName = $subsystem->getName() . ' (' . $subsystem->getHostName();
//			if (null !== ($contextPath = $subsystem->getContextPath())) {
//				$displayName .= '/' . $contextPath;
//			}
//			$displayName .= ')';
//
//			$options[$subsystem->getName()] = $displayName;
//		}
//
//		$this->enumConfig->setOptions($options);
//	}
//}
