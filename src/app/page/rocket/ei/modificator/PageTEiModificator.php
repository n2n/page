<?php
namespace page\rocket\ei\modificator;

use rocket\impl\ei\component\modificator\adapter\IndependentEiModificatorAdapter;
use rocket\ei\util\Eiu;
use n2n\core\config\WebConfig;

class PageTEiModificator extends IndependentEiModificatorAdapter  {

	public function setupGuiDefinition(Eiu $eiu) { 
		if (1 >= count($eiu->lookup(WebConfig::class)->getAllN2nLocales())) {
			$eiu->engine()->removeGuiProp('active');
		}	
	}
}