<?php
namespace page\bo;

use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\l10n\N2nLocale;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use rocket\impl\ei\component\prop\translation\Translatable;
use page\model\PageMonitor;
use n2n\persistence\orm\annotation\AnnoEntityListeners;
use rocket\attribute\EiType;
use rocket\attribute\EiPreset;
use rocket\attribute\impl\EiSetup;
use rocket\op\ei\util\Eiu;
use page\rocket\ei\field\PageTypeEiPropNature;
use page\rocket\ei\field\PagePathEiPropNature;
use n2n\persistence\orm\attribute\Transient;
use rocket\attribute\impl\EiPropBool;
use n2n\reflection\property\PropertiesAnalyzer;
use page\rocket\ei\field\PageSubsystemEiPropNature;
use rocket\attribute\impl\EiPropPathPart;

#[EiType]
#[EiPreset(editProps: [
	'name', 'active' => 'Sprache online', 'title' => 'Titel', 'home' => 'Startseite', 'pathPart' => 'Pfad Teil'
])]
class PageT extends ObjectAdapter implements Translatable {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoEntityListeners(PageEntityListener::getClass()));
		$ai->p('page', new AnnoManyToOne(Page::getClass()));
	}

	private $id;
	private $n2nLocale;
	private string $name;
	private ?string $title = null;
	#[EiPropPathPart(baseProp: 'name')]
	private ?string $pathPart = null;
	private $page;
	private bool $active = true;


	/**
	 * This is a temporary hack util a prop with only getter and/or setter methods can be annotated.
	 *
	 * @var bool $home
	 */
	#[Transient]
	#[EiPropBool(offGuiProps: ['pathPart'])]
	private bool $home;

	private function _prePersist(PageMonitor $pageMonitor) {
		$pageMonitor->registerRelatedChange($this->page);
	}

	private function _preUpdate(PageMonitor $pageMonitor) {
		$pageMonitor->registerRelatedChange($this->page);
	}

	private function _preRemove(PageMonitor $pageMonitor) {
		$pageMonitor->registerRelatedChange($this->page);
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getN2nLocale() {
		return $this->n2nLocale;
	}

	public function setN2nLocale(N2nLocale $n2nLocale) {
		$this->n2nLocale = $n2nLocale;
	}

	public function getName() {
		return $this->name ?? null;
	}

	public function setName(string $name) {
		$this->name = $name;
	}

	public function isHome() {
		return $this->pathPart === null && $this->id !== null;
	}

	public function setHome(bool $home) {
		if ($home) {
			$this->pathPart = null;
		}
	}

	public function getPathPart() {
		return $this->pathPart;
	}

	public function setPathPart($pathPart) {
		$this->pathPart = $pathPart;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getRealTitle() {
		if ($this->title !== null) {
			return $this->title;
		}

		return $this->name;
	}

	public function getPage() {
		return $this->page;
	}

	public function setPage(Page $page) {
		$this->page = $page;
	}

	public function isActive(): bool {
		return $this->active;
	}

	public function setActive(bool $active) {
		$this->active = $active;
	}

	public function getHeading() {
		if (!$this->title) return $this->name;

		return $this->title;
	}

	#[EiSetup]
	static function eiSetup(Eiu $eiu) {
		$eiu->mask()->addProp(new PagePathEiPropNature('Pfad'), 'pagePath');
	}
}