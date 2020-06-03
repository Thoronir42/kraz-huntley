<?php declare(strict_types=1);

namespace SeStep\Executives\Console;

use Nette\Localization\ITranslator;
use SeStep\Executives\ModuleAggregator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecutivesRegistryListCommand extends Command
{
    /** @var string */
    private $listingType;

    /** @var ModuleAggregator */
    private $moduleAggregator;
    /** @var ITranslator */
    private $translator;

    /**
     * @param string $name
     * @param string $listingType 'action'|'condition'
     * @param ModuleAggregator $moduleAggregator
     * @param ITranslator $translator
     */
    public function __construct(
        string $name,
        string $listingType,
        ModuleAggregator $moduleAggregator,
        ITranslator $translator
    ) {
        parent::__construct($name);
        $this->listingType = $listingType;
        $this->moduleAggregator = $moduleAggregator;
        $this->translator = $translator;
        if ($listingType !== 'action' && $listingType !== 'condition') {
            throw new \InvalidArgumentException();
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['code', 'name']);
        $items = $this->listingType === 'action'
            ? $this->moduleAggregator->getActionsPlaceholders()
            : $this->moduleAggregator->getConditionsPlaceholders();

        foreach ($items as $code => $placeholder) {
            $table->addRow([$code, $this->translator->translate($placeholder)]);
        }

        $table->render();
    }


}
