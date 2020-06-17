<?php declare(strict_types=1);

namespace SeStep\Typeful\Console;

use Nette\Localization\ITranslator;
use SeStep\Typeful\Service\EntityDescriptorRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListEntitiesCommand extends Command
{
    /** @var EntityDescriptorRegistry */
    private $descriptorRegistry;
    /** @var ITranslator */
    private $translator;

    public function __construct(string $name, EntityDescriptorRegistry $descriptorRegistry, ITranslator $translator)
    {
        parent::__construct($name);
        $this->descriptorRegistry = $descriptorRegistry;
        $this->translator = $translator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Code', 'Entity']);
        foreach ($this->descriptorRegistry->getDescriptors() as $name => $descriptor) {
            $table->addRow([$name, $this->translator->translate($name)]);
        }

        $table->render();
    }
}
