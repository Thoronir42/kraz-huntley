<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use App\Grid\DecoratorDataSource;
use App\LeanMapper\Exceptions\ValidationException;
use CP\TreasureHunt\Model\Entity\Attributes\TreasureMapFileAttributes;
use CP\TreasureHunt\Model\Entity\TreasureMap;
use CP\TreasureHunt\Model\Service\TreasureMapsService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;
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

    /** @var ITranslator @inject */
    public $translator;

    /** @var TreasureMap */
    private $map;


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
        $this->map = $map = $this->treasureMapsService->getMap($id);

        if (!$map) {
            throw new BadRequestException();
        }

        /** @var Form $treasureMapForm */
        $treasureMapForm = $this['treasureMapForm'];

        $treasureMapForm['filename']->setRequired($map->fileAttributes === null);
        if (!$map->fileAttributes) {
            if (!$treasureMapForm->isSubmitted()) {
                $treasureMapForm['filename']->addError('messages.invalidFile');
            }
        }


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
        $treasureMapForm['id']->setOmitted()->controlPrototype->readonly(true);

        $this->template->map = $map;
    }

    protected function beforeRender()
    {
        $this->layout = 'meta';
    }

    public function renderIndex()
    {
        $grid = $this->entityGridFactory->create(TreasureMap::class, ['name']);
        $grid->setTranslator($this->translator);

        $grid->addColumnText('filename', 'appTreasureHunt.treasureMap.filename')
            ->setRenderer(function (TreasureMap $map) {
                if (!$map->fileAttributes) {
                    return Html::fromHtml('<p class="text-danger">' . $this->translator->translate('messages.invalidFile') . '</p>');
                }
                // todo: Provide lazy file attributes
                $fileAttributes = $map->fileAttributes;
                return Html::fromHtml(<<<HTML
<div>
  <span>{$map->filename}</span> <span class="dimensions">{$fileAttributes->width}px*{$fileAttributes->height}px</span>
</div>
HTML
                );
            });

        $grid->setDataSource($this->treasureMapsService->getDataSource(true));
        
        $grid->setPagination(false);
        $grid->addAction('detail', 'messages.edit', 'detail');

        $this['treasureMapsGrid'] = $grid;
    }

    public function createComponentTreasureMapForm()
    {
        $form = new Form();
        $form->addText('id', 'appTreasureHunt.treasureMap.id');
        $this->entityFormPopulator->fillFromReflection($form, TreasureMap::class);

        $form->addSubmit('save', 'messages.save');
        $form->setTranslator($this->translator);

        return $form;
    }

    public function handlePreviewMap()
    {
        $this->forward('Clue:map', $this->map->id);
    }
}
