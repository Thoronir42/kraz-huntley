<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components;

use CP\TreasureHunt\Model\Entity\Narrative;
use LeanMapper\IMapper;
use Nette\Application\UI\Form;
use SeStep\Typeful\Forms\EntityFormPopulator;

class NarrativeFormFactory
{
    /** @var EntityFormPopulator */
    private $entityFormFactory;
    /** @var IMapper */
    private $mapper;

    public function __construct(EntityFormPopulator $entityFormFactory, IMapper $mapper)
    {
        $this->entityFormFactory = $entityFormFactory;
        $this->mapper = $mapper;
    }

    public function create(bool $newInstance = true)
    {
        $form = new Form();
        $this->entityFormFactory->fillFromReflection($form, Narrative::class, ['title', 'content']);

        $form->addSubmit('send', $newInstance ? 'create' : 'update');

        return $form;
    }
}
