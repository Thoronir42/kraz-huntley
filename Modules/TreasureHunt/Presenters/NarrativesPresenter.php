<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Components\NarrativeFormFactory;
use CP\TreasureHunt\Components\NarrativesGridFactory;
use CP\TreasureHunt\Model\Entity\Narrative;
use CP\TreasureHunt\Model\Service\NarrativesService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class NarrativesPresenter extends Presenter
{
    /** @var NarrativesService @inject */
    public $narrativesService;

    /** @var NarrativesGridFactory @inject */
    public $gridFactory;
    /** @var NarrativeFormFactory @inject */
    public $narrativeFormFactory;

    public function actionIndex()
    {
        $grid = $this->gridFactory->create();
        $grid->setDataSource($this->narrativesService->getNarrativesDataSource());

        $grid->addAction('edit', 'Upravit', 'edit', ['narrativeId' => 'id']);

        $this['narrativesGrid'] = $grid;
    }

    public function actionCreateNew()
    {
        $this->setView('edit');

        $form = $this['narrativeForm'] = $this->narrativeFormFactory->create();

        $form->onSuccess[] = function (Form $form, $values) {
            $narrative = new Narrative();
            $narrative->assign($values);

            $this->narrativesService->save($narrative);
            $this->redirect('index');
        };
    }

    public function actionEdit(string $narrativeId)
    {
        $narrative = $this->narrativesService->getNarrative($narrativeId);
        if (!$narrative) {
            throw new BadRequestException();
        }

        $form = $this['narrativeForm'] = $this->narrativeFormFactory->create(false);

        $form->setDefaults($narrative->getData());

        $form->onSuccess[] = function (Form $form, $values) use ($narrative) {
            $narrative->assign($values);

            $this->narrativesService->save($narrative);
            $this->redirect('this');
        };
    }

    protected function beforeRender()
    {
        $this->layout = 'meta';
    }

}
