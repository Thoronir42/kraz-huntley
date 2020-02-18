<?php declare(strict_types=1);

namespace CP\TreasureHunt\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;

class EmojiMatrixControl extends BaseControl
{
    private static $MATRIX = [
        [7 => '🤑', 13 => '🤯', 17 => '🤠', 11 => '🧐'],
        [2 => '☠', 47 => '🐺', 15 => '🦁'],
        [3 => '🐮', 8 => '🐭', 31 => '🐴', 69 => '🦄', 48 => '🐄'],
    ];

    public function getControl()
    {
        $this->setOption('rendered', true);

        $wrapper = Html::el('div', ['class' => 'pass-matrix']);
        $value = $this->getValue() ?? [];

        foreach (self::$MATRIX as $row) {
            $rowEl = Html::el('div', ['class' => 'pass-matrix-row']);
            foreach ($row as $id => $text) {
                $checkBox = Html::el('input', [
                    'type' => 'checkbox',
                    'name' => $this->getHtmlName() . "-$id",
                ]);
                if (in_array($id, $value)) {
                    $checkBox->checked(in_array($id, $value));
                }
                $label = Html::el('label');
                $label[] = $checkBox;

                $caption = Html::el('span');
                $caption->setText($text);
                $label[] = $caption;

                $rowEl[] = $label;
            }

            $wrapper[] = $rowEl;
        }

        return $wrapper;
    }

    public function loadHttpData(): void
    {
        $value = [];
        foreach (self::$MATRIX as $row) {
            foreach ($row as $i => $label) {
                $data = $this->getHttpData(Form::DATA_TEXT, "-$i");
                if ($data) {
                    $value[] = $i;
                }
            }
        }
        $this->setValue($value);
    }


}
