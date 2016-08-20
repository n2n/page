<?php
namespace page\config;

use n2n\util\config\Attributes;
use n2n\core\module\ConfigDescriberAdapter;
use n2n\web\dispatch\mag\MagCollection;
use n2n\impl\web\dispatch\mag\model\StringArrayMag;
use n2n\impl\web\dispatch\mag\model\BoolMag;
use n2n\core\N2N;
use n2n\impl\web\dispatch\mag\model\MagForm;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\util\config\LenientAttributeReader;
use page\bo\PageController;
use rocket\spec\ei\component\field\impl\ci\conf\CiConfigUtils;
use n2n\impl\web\dispatch\mag\model\MagCollectionMag;
use rocket\core\model\Rocket;
use n2n\reflection\CastUtils;
use rocket\spec\config\SpecManager;
use page\model\PageControllerAnalyzer;
use n2n\impl\web\dispatch\mag\model\StringMag;
use n2n\reflection\property\TypeConstraint;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\component\field\impl\ci\model\PanelConfig;

class PageDescriber extends ConfigDescriberAdapter {
	const ATTR_LOCALES_ACTIVE_KEY = 'n2nLocaleUrls';
	const ATTR_LOCALES_ACTIVE_DEFAULT = true;
	const ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_KEY = 'autoN2nLocaleRedirect';
	const ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_DEFAULT = true;
	const ATTR_SSL_SELECTABLE_KEY = 'sslSelectable';
	const ATTR_SSL_SELECTABLE_DEFAULT = false;
	const ATTR_SSL_DEFAULT_KEY = 'sslDefault';
	const ATTR_SSL_DEFAULT_DEFAULT = true;
	const ATTR_HOOK_KEYS_KEY = 'hooks';
	const ATTR_CACHE_CLEARED_ON_PAGE_EVENT_KEY = 'cacheClearedOnPageEvent';
	const ATTR_CACHE_CLEARED_ON_PAGE_EVENT_DEFAULT = true;
	const ATTR_PAGE_LISTENER_LOOKUP_IDS_KEY = 'pageListenerLookupIds';
	const ATTR_PAGE_CONTROLLERS_KEY = 'pageControllers';
	const ATTR_PAGE_CONTROLLER_LABEL_KEY = 'label';
	const ATTR_PAGE_CONTROLLER_CI_PANELS_KEY = 'ciPanels';
	
	public function createMagDispatchable(): MagDispatchable {
		$lar = new LenientAttributeReader($this->readCustomAttributes());
		
		$magCollection = new MagCollection();
		
		$magCollection->addMag(new BoolMag(self::ATTR_LOCALES_ACTIVE_KEY, 
				'N2nLocales active (if checked, the locales will appear in the URL)',
				$lar->getBool(self::ATTR_LOCALES_ACTIVE_KEY, self::ATTR_LOCALES_ACTIVE_DEFAULT)));
		
		$magCollection->addMag(new BoolMag(self::ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_KEY, 
				'Auto locale Redirect',
				$lar->getBool(self::ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_KEY, 
						self::ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_DEFAULT)));
		
		$magCollection->addMag(new BoolMag(self::ATTR_SSL_SELECTABLE_KEY, 
				'Ssl Option in Page Navi Available?',
				$lar->getBool(self::ATTR_SSL_SELECTABLE_KEY, self::ATTR_SSL_SELECTABLE_DEFAULT)));
		
		$magCollection->addMag(new BoolMag(self::ATTR_SSL_DEFAULT_KEY, 'Default Value for ssl', 
				$lar->getBool(self::ATTR_SSL_DEFAULT_KEY, self::ATTR_SSL_DEFAULT_DEFAULT)));
		
		$magCollection->addMag(new StringArrayMag(self::ATTR_HOOK_KEYS_KEY, 
				'Available Hooks', $lar->getScalarArray(self::ATTR_HOOK_KEYS_KEY)));

		$magCollection->addMag(new BoolMag(self::ATTR_SSL_SELECTABLE_KEY,
				'Clear Cache on Page Event',
				$lar->getBool(self::ATTR_CACHE_CLEARED_ON_PAGE_EVENT_KEY, self::ATTR_CACHE_CLEARED_ON_PAGE_EVENT_DEFAULT)));
		
		$magCollection->addMag(new StringArrayMag(self::ATTR_PAGE_LISTENER_LOOKUP_IDS_KEY, 
				'Page Listener Lookup Ids', $lar->getScalarArray(self::ATTR_PAGE_LISTENER_LOOKUP_IDS_KEY)));
		
		$magCollection->addMag(new MagCollectionMag(self::ATTR_PAGE_CONTROLLERS_KEY, 'PageControllers', 
				$this->createPcMagCollection($lar->getArray(self::ATTR_PAGE_CONTROLLERS_KEY))));
				
		return new MagForm($magCollection);
	}
		
	private function createPcMagCollection(array $pageControllersAttrs) {
		$specManager = $this->n2nContext->lookup(Rocket::class)->getSpecManager();
		CastUtils::assertTrue($specManager instanceof SpecManager);
		
		$pageControllerEiSpec = $specManager->getEiSpecByClass(PageController::getClass());
		
		$ciConfigUtils = CiConfigUtils::createFromN2nContext($this->n2nContext);
		
		$lar = new LenientAttributeReader(new Attributes($pageControllersAttrs));
		
		$magCollection = new MagCollection();
		foreach ($pageControllerEiSpec->getAllSubEiSpecs() as $subEiSpec) {
			$pageControllerAttrs = $lar->getArray($subEiSpec->getId());
			$pcLar = new LenientAttributeReader(new Attributes($pageControllerAttrs));
			
			$pcMagCollection = new MagCollection();
			$magCollection->addMag(new MagCollectionMag($subEiSpec->getId(), $subEiSpec->getEiMaskCollection()
					->getOrCreateDefault()->getLabel(), $pcMagCollection));
			
// 			$pcMagCollection->addMag(new StringMag(self::ATTR_PAGE_CONTROLLER_LABEL_KEY, 'Label', 
// 					$pcLar->getString(self::ATTR_PAGE_CONTROLLER_LABEL_KEY)));
			
			$panelNames = (new PageControllerAnalyzer($subEiSpec->getEntityModel()->getClass()))->analyzeAllCiPanelNames();
			if (empty($panelNames)) {
				continue;
			}
			
			$panelsAttrs = $pcLar->getArray(self::ATTR_PAGE_CONTROLLER_CI_PANELS_KEY);
			$panelsMagCollection = new MagCollection();
			foreach ($panelNames as $panelName) {
				$panelMagCollection = $ciConfigUtils->createPanelConfigMagCollection(false);
				if (isset($panelsAttrs[$panelName])) {
					$panelMagCollection->writeValues($ciConfigUtils->buildPanelConfigMagCollectionValues(
							$panelsAttrs[$panelName]));
				}
				$panelsMagCollection->addMag(new MagCollectionMag($panelName, $panelName, $panelMagCollection));	
			}
			$pcMagCollection->addMag(new MagCollectionMag(self::ATTR_PAGE_CONTROLLER_CI_PANELS_KEY, 'Panels', $panelsMagCollection));
		}
	
		return $magCollection;
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable) {
		$attributes = new Attributes($magDispatchable->getMagCollection()->readValues());
		$attributes->removeNulls(true);
		$this->writeCustomAttributes($attributes);
	}
	
	/**
	 * @see \n2n\core\module\DescriberAdapter::createCustomConfig()
	 * 
	 * @return \page\config\PageConfig
	 */
	public function buildCustomConfig() {
		$attributes = $this->readCustomAttributes();
		
		$pageConfig = new PageConfig();
		$pageConfig->setN2nLocaleUrlsActive($attributes->getBool(self::ATTR_LOCALES_ACTIVE_KEY, false,
				self::ATTR_LOCALES_ACTIVE_DEFAULT));
		$pageConfig->setAutoN2nLocaleRedirectAllowed($attributes->getBool(self::ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_KEY, 
				false, self::ATTR_AUTO_LOCALE_REDIRECT_ACTIVE_DEFAULT));
		$pageConfig->setSslDefault($attributes->getBool(self::ATTR_SSL_SELECTABLE_KEY, false,
				self::ATTR_SSL_SELECTABLE_DEFAULT));
		$pageConfig->setSslDefault($attributes->getBool(self::ATTR_SSL_DEFAULT_KEY, false,
				self::ATTR_SSL_DEFAULT_DEFAULT));
		
		$hooks = array();
		foreach ($attributes->getScalarArray(self::ATTR_HOOK_KEYS_KEY, false) as $key => $label) {
			if (is_numeric($key)) {
				$hooks[$label] = $label;
			} else {
				$hooks[$key] = $label;
			}
		}
		$pageConfig->setHooks($hooks);
		$pageConfig->setPageListenerLookupIds($attributes->getScalarArray(self::ATTR_PAGE_LISTENER_LOOKUP_IDS_KEY, 
				false));
		
		$pageControllerConfigs = array();
		foreach ($attributes->getArray(self::ATTR_PAGE_CONTROLLERS_KEY, false, array(), 
				TypeConstraint::createArrayLike('array')) as $pageControllerEiSpecId => $pageControllerAttrs) {
			$ciPanelConfigs = array();
			$pageControllerAttributes = new Attributes($pageControllerAttrs);
			foreach ($pageControllerAttributes->getArray(self::ATTR_PAGE_CONTROLLER_CI_PANELS_KEY, false, array(), 
					TypeConstraint::createArrayLike('array')) as $panelName => $ciPanelAttrs) {
				$ciPanelConfigs[] = CiConfigUtils::createPanelConfig($ciPanelAttrs, $panelName);
			}
				
			$pageConfig->addPageControllerConfig(new PageControllerConfig($pageControllerEiSpecId, $ciPanelConfigs));
		}
		
		return $pageConfig;
	}
}

class PageControllerConfig {
	private $eiSpecId;
	private $ciPanelConfigs;
	
	public function __construct(string $eiSpecId, array $ciPanelConfigs) {
		ArgUtils::valArray($ciPanelConfigs, PanelConfig::class);
		$this->eiSpecId = $eiSpecId;
		$this->ciPanelConfigs = $ciPanelConfigs;
	}
	
	public function getEiSpecId() {
		return $this->eiSpecId;
	}
	
	public function getCiPanelConfigs() {
		return $this->ciPanelConfigs;
	}
	

	public function getCiPanelConfigByPanelName(string $panelName) {
		foreach ($this->ciPanelConfigs as $ciPanelConfig) {
			if ($ciPanelConfig->getName() === $panelName) {
				return $ciPanelConfig;
			}
		}
		
		return null;
	}
	
}