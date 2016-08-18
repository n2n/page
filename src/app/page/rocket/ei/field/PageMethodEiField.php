<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\adapter\DraftableEiFieldAdapter;
use rocket\spec\ei\manage\gui\FieldSourceInfo;
use n2n\reflection\CastUtils;
use page\bo\PageController;
use n2n\dispatch\mag\impl\model\EnumMag;
use page\model\PageControllerAnalyzer;
use n2n\util\StringUtils;
use rocket\spec\ei\manage\EiObject;
use n2n\l10n\N2nLocale;
use rocket\spec\ei\EiFieldPath;
use n2n\ui\view\impl\html\HtmlView;
use n2n\dispatch\mag\Mag;
use n2n\dispatch\map\PropertyPath;
use n2n\ui\UiComponent;

class PageMethodEiField extends DraftableEiFieldAdapter {
	
	public function createMag(string $propertyName, FieldSourceInfo $fieldSourceInfo): Mag {
		$pageController = $fieldSourceInfo->getEiMapping()->getEiSelection()->getLiveObject();
		CastUtils::assertTrue($pageController instanceof PageController);
		
		$analyzer = new PageControllerAnalyzer(new \ReflectionClass($pageController));
		
		$ciPanelNames = array();
		$options = array();
		foreach ($analyzer->analyzeAllMethods() as $pageMethod) {
			$ciPanelNames[$pageMethod->getName()] = $pageMethod->getCiPanelNames();
			$options[$pageMethod->getName()] = StringUtils::pretty($pageMethod->getName());
		}
		
		$mag = new PageMethodEnumMag($propertyName, $this->getLabelLstr(), $options, null, 
				$this->isMandatory($fieldSourceInfo));
		$mag->setInputAttrs(array('class' => 'page-method', 'data-panel-names' => json_encode($ciPanelNames)));
		return $mag;
	}
	
	public function createOutputUiComponent(HtmlView $view, FieldSourceInfo $fieldSourceInfo)  {
		return $view->getHtmlBuilder()->getEsc(StringUtils::pretty(
				$fieldSourceInfo->getValue(EiFieldPath::from($this))));
	}
		
	public function isStringRepresentable(): bool {
		return true;
	}
	
	public function buildIdentityString(EiObject $eiObject, N2nLocale $n2nLocale) {
		return $this->read($eiObject);
	}
}

class PageMethodEnumMag extends EnumMag {
	
	public function createUiField(PropertyPath $propertyPath, HtmlView $view): UiComponent {
		$view->getHtmlBuilder()->meta()->addJs('js/page-method.js', 'page');
		
		return parent::createUiField($propertyPath, $view);
	}
}