<?php declare(strict_types=1);

namespace DatabaseCli;

use Dibi\Connection;
use Nette\FileNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDatabaseCommand extends Command
{
    /** @var Connection */
    private $connection;
    /** @var string[] */
    private $defaultFiles;

    /** @var string[] */
    private $files = [];

    private $currentFile;
    private $currentQueries;
    private $currentQueryIndex;

    private $maxLengthProgress;

    public function __construct(Connection $connection, iterable $defaultFiles, string $commandName)
    {
        parent::__construct($commandName);

        $this->connection = $connection;
        $this->defaultFiles = $defaultFiles;
    }

    public function configure()
    {
        $this->addOption('default-files', 'd', null, "Use predefined database initialize files");
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('default-files')) {
            $output->writeln("Using default files");
            $this->files = $this->defaultFiles;
        }
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($this->files)) {
            $output->writeln("No files specified!");
            return 1;
        }

        $output->writeln("Initializing database ...");

        foreach ($this->files as $path) {
            try {
                $this->currentFile = $path;
                $this->executeCurrentFile($output);

                $this->writeFileProgress($output, ' ok');
            } catch (\Throwable $ex) {
                $this->writeFileProgress($output, $this->getQueryProgress() . ' error');
                throw $ex;
            } finally {
                $output->writeln("");
            }
        }

        $output->writeln("Database initialized");
        return 0;
    }

    private function executeCurrentFile(OutputInterface $output)
    {
        if (!file_exists($this->currentFile)) {
            $this->writeFileProgress($output, "file not found");
            throw new FileNotFoundException("File '$this->currentFile' could not be found");
        }

        $script = file_get_contents($this->currentFile);
        $this->currentQueries = array_filter(explode(';', $script), function ($query) {
            return !empty(trim($query));
        });
        $this->currentQueryIndex = 0;
        $this->maxLengthProgress = 0;

        while ($this->currentQueryIndex < count($this->currentQueries)) {
            $this->writeFileProgress($output, $this->getQueryProgress());
            $this->connection->nativeQuery($this->currentQueries[$this->currentQueryIndex] . ';');

            $this->currentQueryIndex++;
        }
    }

    private function getQueryProgress()
    {
        return '[' . $this->currentQueryIndex . '/' . count($this->currentQueries) . ']';
    }

    private function writeFileProgress(OutputInterface $output, $progress)
    {
        $options = OutputInterface::VERBOSITY_VERBOSE;

        $output->write("\r", false, $options);
        $progressStr = "  - $this->currentFile $progress";
        $output->write($progressStr, false, $options);

        $len = mb_strlen($progressStr);
        if ($len < $this->maxLengthProgress) {
            $spaceCleaner = str_repeat(" ", $this->maxLengthProgress - $len);
            $output->write($spaceCleaner, false, $options);
        } else {
            $this->maxLengthProgress = $len;
        }
    }


}
