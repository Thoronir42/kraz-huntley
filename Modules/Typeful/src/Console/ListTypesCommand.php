<?php declare(strict_types=1);

namespace SeStep\Typeful\Console;

use Nette\Localization\ITranslator;
use SeStep\Typeful\Service\TypeRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTypesCommand extends Command
{
    /** @var TypeRegistry */
    private $typeRegistry;
    /** @var ITranslator */
    private $translator;

    public function __construct(string $name, TypeRegistry $descriptorRegistry, ITranslator $translator)
    {
        parent::__construct($name);
        $this->typeRegistry = $descriptorRegistry;
        $this->translator = $translator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Code', 'Type']);
        foreach ($this->typeRegistry->getTypesLocalized() as $type) {
            $table->addRow([$type, $this->translator->translate($type)]);
        }

        $table->render();
    }
}
