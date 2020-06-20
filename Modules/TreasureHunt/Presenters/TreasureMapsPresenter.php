<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\LeanMapper\Exceptions\ValidationException;
use CP\TreasureHunt\Model\Entity\TreasureMap;
use CP\TreasureHunt\Model\Service\TreasureMapsService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use SeStep\NetteTypeful\Components\EntityGridFactory;
use SeStep\NetteTypeful\Forms\EntityFormPopulator;

class TreasureMapsPresenter extends Presenter
{

    /** @var EntityGridFactory @inject */
    public $entityGridFactory;
    /** @var EntityFormPopulator @inject */
    public $entityFormPopulator;

    /** @var TreasureMapsService @inject */
    public $treasureMapsService;

    public function actionCreateNew()
    {
        /** @var Form $treasureMapForm */
        $treasureMapForm = $this['treasureMapForm'];

        $treasureMapForm->onSuccess[] = function (Form $form, $values) {
            try {
                $map = $this->treasureMapsService->create($values);
            } catch (\Throwable $ex) {
                bdump($ex);
                // todo: propagate exception
                $this->flashMessage('messages.error');
                return;
            }

            $this->redirect('detail', $map->id);
        };
        $this->setView('detail');
    }

    public function actionDetail(string $id)
    {
        $map = $this->treasureMapsService->getMap($id);

        if (!$map) {
            throw new BadRequestException();
        }

        /** @var Form $treasureMapForm */
        $treasureMapForm = $this['treasureMapForm'];

        $treasureMapForm->onSuccess[] = function (Form $form, $values) use ($map) {
            unset($values['id']);
            try {
                $this->treasureMapsService->update($map, $values);
            } catch (ValidationException $exception) {
                foreach ($exception->getErrors() as $field => $error) {
                    $form[$field]->addError($error);
                }
                bdump($exception);
                return;
            }

            $this->redirect('detail', $map->id);
        };

        $treasureMapForm->setDefaults($map->getData());
        $treasureMapForm['id']->setDisabled();
    }

    public function renderIndex()
    {
        $grid = $this->entityGridFactory->create(TreasureMap::class, ['name']);
        $grid->addColumnText('filename', 'filename')
            ->setRenderer(function (TreasureMap $map) {
                return <<<HTML
<div>
  <span>{$map->filename}</span> <span class="dimensions">{$map->getWidth()}x{$map->getHeight()}</span>
</div>
HTML;
            });
        $grid->setDataSource($this->treasureMapsService->getDataSource());

        $grid->setItemsPerPageList(['all']);
        $grid->addAction('detail', 'Upravit', 'detail');

        $this['treasureMapsGrid'] = $grid;
    }

    public function createComponentTreasureMapForm()
    {
        $form = new Form();
        $form->addText('id', 'appTreasureHunt.map.id');
        $this->entityFormPopulator->fillFromReflection($form, TreasureMap::class);

        $form->addSubmit('save', 'messages.save');
        $form->setTranslator($this->context->getByType(ITranslator::class));

        return $form;
    }
}
