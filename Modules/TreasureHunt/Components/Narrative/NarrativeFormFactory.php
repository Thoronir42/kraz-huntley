<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Narrative;

use CP\TreasureHunt\Model\Entity\Narrative;
use LeanMapper\IMapper;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use SeStep\NetteTypeful\Forms\EntityFormPopulator;

class NarrativeFormFactory
{
    /** @var EntityFormPopulator */
    private $entityFormFactory;
    /** @var IMapper */
    private $mapper;
    /** @var ITranslator */
    private $translator;

    public function __construct(EntityFormPopulator $entityFormFactory, IMapper $mapper, ITranslator $translator)
    {
        $this->entityFormFactory = $entityFormFactory;
        $this->mapper = $mapper;
        $this->translator = $translator;
    }

    public function create(bool $newInstance = true)
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $this->entityFormFactory->fillFromReflection($form, Narrative::class, ['title', 'content']);

        $form->addSubmit('save', $newInstance ? 'messages.create' : 'messages.update');

        return $form;
    }
}
