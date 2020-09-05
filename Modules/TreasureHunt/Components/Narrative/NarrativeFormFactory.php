<?php declare(strict_types=1);

namespace CP\TreasureHunt\Components\Narrative;

use CP\TreasureHunt\Model\Entity\Narrative;
use CP\TreasureHunt\Model\Service\ChallengesService;
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
    /** @var ChallengesService */
    private $challengesService;

    public function __construct(EntityFormPopulator $entityFormFactory, IMapper $mapper, ITranslator $translator, ChallengesService $challengesService)
    {
        $this->entityFormFactory = $entityFormFactory;
        $this->mapper = $mapper;
        $this->translator = $translator;
        $this->challengesService = $challengesService;
    }

    public function create(bool $newInstance = true)
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $this->entityFormFactory->fillFromReflection($form, Narrative::class, ['title', 'content']);
        $form->addSelect('followingChallenge', 'appTreasureHunt.narrative.followingChallenge')
            ->setItems($this->challengesService->getNames());

        $form->addSubmit('save', $newInstance ? 'messages.create' : 'messages.update');

        return $form;
    }
}
