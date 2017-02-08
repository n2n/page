<?php
namespace page\rocket\ei\field;

use n2n\persistence\orm\property\EntityProperty;
use page\bo\Page;
use n2n\impl\persistence\orm\property\ToManyEntityProperty;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\component\field\impl\ci\model\ContentItem;
use page\bo\PageControllerT;
use rocket\spec\ei\manage\util\model\Eiu;
use rocket\spec\ei\component\field\impl\ci\ContentItemsEiField;
use rocket\spec\ei\EiFieldPath;
use n2n\reflection\CastUtils;
use page\bo\PageController;
use page\model\PageControllerAnalyzer;
use page\config\PageConfig;
use rocket\spec\ei\component\field\impl\ci\model\PanelConfig;
use n2n\util\StringUtils;
use rocket\core\model\Rocket;
use rocket\spec\ei\component\field\impl\ci\model\ContentItemGuiElement;

class PageContentItemsEiField extends ContentItemsEiField {
	/**
	 * @var \page\config\PageConfig
	 */
	private $pageConfig;
	
	public function getTypeName(): string {
		return 'ContentItems (Page)';
	}
	
	public function setEntityProperty(EntityProperty $entityProperty = null) {
		parent::setEntityProperty($entityProperty);
		
		ArgUtils::assertTrue($entityProperty instanceof ToManyEntityProperty
				&& $entityProperty->getEntityModel()->getClass()->getName() === PageControllerT::class
				&& $entityProperty->getTargetEntityModel()->getClass()->getName() === ContentItem::class);
	}
	
	public function determinePanelConfigs(Eiu $eiu) {
		$relationMapping = $eiu->entry()->getEiMapping()->getValue(EiFieldPath::from($this)->poped()
				->pushed('pageController'));
		if ($relationMapping === null) {
			return array();
		}
		$pageController = $relationMapping->getEiSelection()->getLiveObject();
		CastUtils::assertTrue($pageController instanceof PageController);
		
		$rocket = $eiu->frame()->getEiState()->getN2nContext()->lookup(Rocket::class);
		CastUtils::assertTrue($rocket instanceof Rocket);
		$specManager = $rocket->getSpecManager();
		
		$pageControllerClass = new \ReflectionClass($pageController);
		$analyzer = new PageControllerAnalyzer($pageControllerClass);
		$pageConfig = $eiu->frame()->getEiState()->getN2nContext()->getModuleConfig(Page::NS);
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		$pageControllerConfig = $pageConfig->getPageControllerConfigByEiSpecId(
				$specManager->getEiSpecByClass($pageControllerClass)->getId());
		
		$panelConfigs = array();
		foreach ($analyzer->analyzeAllCiPanelNames() as $panelName) {
			if ($pageControllerConfig !== null && 
					null !== ($panelConfig = $pageControllerConfig->getCiPanelConfigByPanelName($panelName))) {
				$panelConfigs[$panelName] = $panelConfig;
				continue;
			}
			
			$panelConfigs[$panelName] = $panelConfig = new PanelConfig($panelName, StringUtils::pretty($panelName));
		}
		return $panelConfigs;
	}
	
	public function buildGuiElement(Eiu $eiu) {
		$contentItemGuiElement = parent::buildGuiElement($eiu);
		CastUtils::assertTrue($contentItemGuiElement instanceof ContentItemGuiElement);
		
		if (empty($contentItemGuiElement->getPanelConfigs())) {
			return null;
		}
		
		return $contentItemGuiElement;
	}
	
}