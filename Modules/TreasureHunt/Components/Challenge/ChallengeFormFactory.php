<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Challenge;

use Contributte\Translation\Translator;
use CP\TreasureHunt\Model\Entity\Challenge;
use Nette\Application\UI\Form;
use SeStep\NetteTypeful\Forms\PropertyControlFactory;
use SeStep\Typeful\Service\TypeRegistry;

class ChallengeFormFactory
{
    /** @var TypeRegistry */
    private $typeRegistry;
    /** @var PropertyControlFactory */
    private $controlFactory;
    /** @var Translator */
    private $translator;
    /** @var array */
    private $types;

    public function __construct(
        TypeRegistry $typeRegistry,
        PropertyControlFactory $controlFactory,
        Translator $translator,
        array $types
    ) {
        $this->typeRegistry = $typeRegistry;
        $this->controlFactory = $controlFactory;
        $this->translator = $translator;
        $this->types = $types;
    }

    public function create()
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $types = array_intersect_key($this->typeRegistry->getTypesLocalized(), array_flip($this->types));

        $form->addText('title', 'th.challenge.title');
        $form->addTextArea('description', 'th.challenge.description');
        $form->addSelect('keyType', 'th.challenge.keyType', $types);

        $form->addSubmit('save', 'save');

        return $form;
    }
}
