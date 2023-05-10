<?php
namespace page\rocket\ei\field\conf;

use n2n\util\type\attrs\DataSet;
use n2n\web\dispatch\mag\MagCollection;

use rocket\op\ei\util\Eiu;
use rocket\impl\ei\component\prop\enum\conf\EnumConfig;
use n2n\util\type\CastUtils;
use page\config\PageConfig;
use rocket\impl\ei\component\prop\adapter\config\PropConfigAdaption;

class PageHookKeyConfig extends PropConfigAdaption {
	private $enumConfig;
	
	function __construct(EnumConfig $enumConfig) {
		$this->enumConfig = $enumConfig;
	}
	
	public function mag(Eiu $eiu, DataSet $dataSet, MagCollection $magCollection) {
	}
	
	public function save(Eiu $eiu, MagCollection $magCollection, DataSet $dataSet) {
		
	}
	
	public function setup(Eiu $eiu, DataSet $dataSet) {
		$pageConfig = $eiu->getN2nContext()->getModuleConfig('page');
		CastUtils::assertTrue($pageConfig instanceof PageConfig);
		
		$this->enumConfig->setOptions($pageConfig->getHooks());
	}
	
}