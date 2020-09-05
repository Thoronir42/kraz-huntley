<?php declare(strict_types=1);

namespace DatabaseCli;

use Dibi\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WipeSchemaCommand extends Command
{
    /** @var Connection */
    private $connection;
    /** @var string */
    private $schemaName;

    public function __construct(Connection $connection, string $schemaName, string $commandName)
    {
        parent::__construct($commandName);
        $this->connection = $connection;
        $this->schemaName = $schemaName;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dropAllTables($output);

        return 0;
    }


    private function dropAllTables(OutputInterface $output)
    {
        $tables = $this->connection->query(
            'SELECT t.table_name FROM information_schema.tables t WHERE t.table_schema = %s',
            $this->schemaName
        )
            ->fetchPairs();

        if (empty($tables)) {
            $output->writeln("Nothing to remove in schema {$this->schemaName}");
            return;
        }

        $output->writeln('Removing ' . count($tables) . ' tables');
        try {
            $output->writeln("Disabling foreign key checks");
            $this->connection->query('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($tables as $table) {
                $output->write(" - deleting table $table... ", false, OutputInterface::VERBOSITY_VERBOSE);
                $this->connection->query('DROP TABLE %n', $table);
                $output->writeln("ok", OutputInterface::VERBOSITY_VERBOSE);
            }
        } catch (\Throwable $ex) {
            $output->writeln("error");
            $output->writeln($ex->getMessage());
        } finally {
            $output->writeln("Enabling foreign key checks");
            $this->connection->query('SET FOREIGN_KEY_CHECKS = 1;');
        }

        $output->writeln("Dropped " . count($tables) . ' tables');
    }
}
