<?php declare(strict_types=1);

namespace CP\TreasureHunt\Typeful\Types;

use Nette\Localization\ITranslator;
use SeStep\Typeful\Types\PropertyType;

class ClueType implements PropertyType
{
    const NARRATIVE = 'narrative';

    /** @var ITranslator */
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function renderValue($value, array $options = [])
    {
        return $this->translator->translate("$value");
    }

    public function getTypes(): array
    {
        return [
            self::NARRATIVE => self::NARRATIVE,
        ];
    }
}
