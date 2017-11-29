<?php
namespace page\rocket\ei\field;

use rocket\spec\ei\component\field\impl\adapter\DraftableEiPropAdapter;
use rocket\spec\ei\manage\util\model\Eiu;
use n2n\reflection\CastUtils;
use page\bo\PageController;
use n2n\impl\web\dispatch\mag\model\EnumMag;
use page\model\PageControllerAnalyzer;
use n2n\util\StringUtils;
use rocket\spec\ei\manage\EiObject;
use n2n\l10n\N2nLocale;
use rocket\spec\ei\EiPropPath;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\dispatch\mag\Mag;
use n2n\web\dispatch\map\PropertyPath;
use n2n\web\ui\UiComponent;

class PageMethodEiProp extends DraftableEiPropAdapter {
	
	public function createMag(Eiu $eiu): Mag {
		$pageController = $eiu->entry()->getEiMapping()->getEiSelection()->getLiveObject();
		CastUtils::assertTrue($pageController instanceof PageController);
		
		$analyzer = new PageControllerAnalyzer(new \ReflectionClass($pageController));
		
		$ciPanelNames = array();
		$options = array();
		foreach ($analyzer->analyzeAllMethods() as $pageMethod) {
			$ciPanelNames[$pageMethod->getName()] = $pageMethod->getCiPanelNames();
			$options[$pageMethod->getName()] = StringUtils::pretty($pageMethod->getName());
		}
		
		$mag = new PageMethodEnumMag($this->getLabelLstr(), $options, null, 
				$this->isMandatory($eiu));
		$mag->setInputAttrs(array('class' => 'page-method', 'data-panel-names' => json_encode($ciPanelNames)));
		return $mag;
	}
	
	public function createOutputUiComponent(HtmlView $view, Eiu $eiu)  {
		return $view->getHtmlBuilder()->getEsc(StringUtils::pretty(
				$eiu->field()->getValue(EiPropPath::from($this))));
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