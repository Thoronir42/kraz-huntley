<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

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

    public function __construct(
        TypeRegistry $typeRegistry,
        PropertyControlFactory $controlFactory,
        Translator $translator
    ) {
        $this->typeRegistry = $typeRegistry;
        $this->controlFactory = $controlFactory;
        $this->translator = $translator;
    }

    public function create()
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $types = array_intersect_key($this->typeRegistry->getTypesLocalized(), $this->controlFactory->getTypes());

        $form->addText('title', 'th.challenge.title');
        $form->addTextArea('description', 'th.challenge.description');
        $form->addSelect('keyType', 'th.challenge.keyType', $types);

        $form->addSubmit('save');

        return $form;
    }
}
