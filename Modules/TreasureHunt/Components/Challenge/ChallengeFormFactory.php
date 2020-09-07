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

        $requestedTypes = array_flip($types);
        $typesLocalized = $this->typeRegistry->getTypesLocalized();

        $missingTypes = array_diff_key($requestedTypes, $typesLocalized);

        if (!empty($missingTypes)) {
            $missingTypesStr = implode("', '", array_keys($missingTypes));
            throw new \InvalidArgumentException("These types are invalid: ['$missingTypesStr']");
        }

        $this->types = array_intersect_key($typesLocalized, $requestedTypes);
    }

    public function create()
    {
        $form = new Form();
        $form->setTranslator($this->translator);

        $form->addText('code', 'appTreasureHunt.challenge.code');
        $form->addText('title', 'appTreasureHunt.challenge.title');
        $form->addTextArea('description', 'appTreasureHunt.challenge.description')
            ->controlPrototype->class[] = 'wysiwyg';

        $form->addSelect('keyType', 'appTreasureHunt.challenge.keyType', $this->types);
        // TODO: Add option to edit options

        $form->addSubmit('save', 'messages.save');

        return $form;
    }
}
