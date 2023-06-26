<?php
namespace page\bo\util;

use n2n\reflection\ObjectAdapter;
use n2n\web\http\nav\UrlComposer;
use n2n\core\container\N2nContext;
use n2n\web\http\controller\ControllerContext;
use n2n\util\uri\Url;
use page\bo\Page;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\persistence\orm\FetchType;
use page\model\nav\murl\MurlPage;
use rocket\attribute\EiType;
use rocket\attribute\EiPreset;
use rocket\op\spec\setup\EiPresetMode;
use rocket\attribute\impl\EiPropEnum;

/**
 * This util entity can be easly intergrated
 *
 */
#[EiType(label: 'Link')]
#[EiPreset(EiPresetMode::EDIT_PROPS, editProps: ['type' => 'Typ', 'linkedPage' => 'Seite', 'url' => 'externer Link', 'label'],
		excludeProps: ['id'])]
class PageLink extends ObjectAdapter implements UrlComposer {
	private static function _annos(AnnoInit $ai) {
		$ai->p('linkedPage', new AnnoManyToOne(Page::getClass(), null, FetchType::EAGER));
	}

	const TYPE_INTERNAL = 'internal';
	const TYPE_EXTERNAL = 'external';

	private int $id;
	#[EiPropEnum([self::TYPE_INTERNAL => 'intern', self::TYPE_EXTERNAL => 'extern'], guiPropsMap: ['type' => ['internal' => ['linkedPage'], 'external' => ['url']]])]
	private string $type = self::TYPE_INTERNAL;
	private ?Page $linkedPage = null;
	private ?string $url = null;
	private ?string $label = null;

	public function getId(): ?int {
		return $this->id ?? null;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getLinkedPage() {
		return $this->linkedPage;
	}

	public function setLinkedPage(Page $linkedPage = null) {
		$this->linkedPage = $linkedPage;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url = null) {
		$this->url = $url;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function toUrl(N2nContext $n2nContext, ControllerContext $controllerContext = null,
			string &$suggestedLabel = null): Url {

		if ($this->type == self::TYPE_EXTERNAL) {
			$suggestedLabel = $this->label;
			return Url::create($this->url);
		}

		$url = MurlPage::obj($this->linkedPage)->toUrl($n2nContext, $controllerContext, $suggestedLabel);
		if ($this->label !== null) {
			$suggestedLabel = $this->label;
		}

		return $url;
	}
}