<?php
namespace page\model;

use n2n\reflection\ReflectionContext;
use page\annotation\AnnoCiPanel;
use page\annotation\AnnoPage;
use page\annotation\AnnoPageCiPanels;

class PageControllerAnalyzer {
	private $class;
	private $as;
	
	public function __construct(\ReflectionClass $class) {
		$this->class = $class;
		$this->as = ReflectionContext::getAnnotationSet($this->class);
	}
	
	/**
	 * @throws PageErrorException
	 * @return PageCiPanel[]
	 */
	public function analyzeAllCiPanelNames() {
		$ciPanelNames = array();
		
		foreach ($this->as->getMethodAnnotationsByName(AnnoPageCiPanels::class) as $annoPageCiPanels) {
			if (!$this->as->hasMethodAnnotation($annoPageCiPanels->getAnnotatedMethod()->getName(), AnnoPage::class)) {
				throw new PageErrorException('Panel assigned on non-page method: ' 
								. implode(', ', $annoPageCiPanels->getNames()),
						$annoPageCiPanels->getFileName(), $annoPageCiPanels->getLine());
			}
			
			foreach ($annoPageCiPanels->getNames() as $panelName) {
				$ciPanelNames[$panelName] = $panelName;
			}
		}
		
		return $ciPanelNames;
	}
	
// 	private function assignCiPanel(array &$ciPanels, AnnoPageCiDef $annoPageCiDef) {
// 		$panelName = $annoPageCiDef->getPanelName();
// 		if (!isset($ciPanels[$panelName])) {
// 			$ciPanels[$panelName] = new PageCiPanel($annoPageCiDef->getPanelName(), 
// 					$annoPageCiDef->getAllowedCiClasses());
// 			return;
// 		}
		
// 		throw new PageErrorException('Panel is defined multiple times: ' . $panelName, 
// 				$annoPageCiDef->getFileName(), $annoPageCiDef->getLine());
// 	}
	
	/**
	 * @return \page\bo\PageMethod[]
	 */
	public function analyzeAllMethods(): array {
		$pageMethods = array();
		foreach ($this->as->getMethodAnnotationsByName(AnnoPage::class) as $annoPage) {
			$pageMethods[] = $this->createPageMethod($annoPage);
		}
		return $pageMethods;
	}
	
	/**
	 * @param unknown $methodName
	 * @return boolean
	 */
	public function containsMethodName($methodName) {
		return $this->as->hasMethodAnnotation($methodName, AnnoPage::class);
	}
	
	/**
	 * @return string[] 
	 */
	public function getMethodNames() {
		return array_keys($this->as->getMethodAnnotationsByName(AnnoPage::class));
	}
	
	/**
	 * @param string $methodName
	 * @return \page\bo\PageMethod
	 */
	public function analyzeMethode(string $methodName) {
		$annoPage = $this->as->getMethodAnnotation($methodName, AnnoPage::class);
		
		if ($annoPage !== null) {
			return $this->createPageMethod($annoPage);
		}
		
		return null;
	}
	
	/**
	 * @param AnnoPage $annoPage
	 * @return \page\bo\PageMethod
	 */
	private function createPageMethod(AnnoPage $annoPage) {
		$methodName = $annoPage->getAnnotatedMethod()->getName();
		
		$pageMethod = new PageMethod($methodName, $annoPage->isUnique());
		
		if (null !== ($annoPageCiPanels = $this->as->getMethodAnnotation($methodName, AnnoPageCiPanels::class))) {
			$pageMethod->setCiPanelNames($annoPageCiPanels->getNames());
		}
		
		return $pageMethod;
	}
		
	private function createPageCiPanel(AnnoCiPanel $annoCiPanel) {
		return new PageCiPanel($annoCiPanel->getName(), 
				$annoCiPanel->getAllowedCiClassNames());
	}
}

class PageControllerException extends \RuntimeException {
	
}