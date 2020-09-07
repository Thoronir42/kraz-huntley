<?php declare(strict_types=1);


namespace CP\TreasureHunt\Controls;


use CP\TreasureHunt\Typeful\Types\PictureSelection;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;

class PictureSelectionControl extends BaseControl
{
    /** @var array */
    private $pictures;
    /** @var int */
    private $slots;
    /** @var string */
    private $baseDirectory;

    public function __construct(array $pictures, int $slots, string $baseDirectory, $label = null)
    {
        parent::__construct($label);
        $this->pictures = $pictures;
        $this->slots = $slots;
        $this->baseDirectory = $baseDirectory;

        $this->control = Html::el('div');
    }

    public function getControl()
    {
        $this->setOption('rendered', true);

        $value = explode('-', $this->getValue() ?? '');

        $el = clone $this->control;
        $el->addAttributes([
            'class' => 'picture-selection',
        ]);
        for ($i = 0; $i < $this->slots; $i++) {
            $el->addHtml($this->createSelectionElement($i, $value[$i] ?? null));
        }

        return $el;
    }

    private function createSelectionElement(int $i, $selectedItem)
    {
        $select = Html::el('select');
        $select->addAttributes([
            'name' => $this->htmlName ."-$i",
        ]);
        foreach ($this->pictures as $value => $filename) {
            $option = Html::el('option', [
                'value' => $value,
            ]);
            if ($value === $selectedItem) {
                $option->setAttribute('selected', true);
            }

            $option->setText($this->baseDirectory . $filename);
            $select->addHtml($option);
        }

        return $select;
    }

    public function loadHttpData(): void
    {
        $value = [];
        for ($i = 0; $i < $this->slots; $i++) {
            $value[$i] = $this->getHttpData(Form::DATA_TEXT, "-$i");
        }

        $this->setValue(implode('-', $value));
    }

    public static function create(string $label, PictureSelection $type, array $options)
    {
        return new self($type->getPictures(), $type->getSlots(), $type->getBaseDirectory(), $label);
    }

}
