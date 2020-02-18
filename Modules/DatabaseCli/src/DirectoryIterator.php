<?php declare(strict_types=1);

namespace DatabaseCli;

class DirectoryIterator implements \IteratorAggregate
{
    /** @var string */
    private $dir;

    public function __construct(string $dir)
    {
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \InvalidArgumentException("'$dir' is not a readable directory");
        }

        $this->dir = trim($dir, '/\\') . '/';
    }

    /** @inheritDoc */
    public function getIterator()
    {
        $files = [];
        foreach (scandir($this->dir) as $fileName) {
            $filePath = "$this->dir$fileName";
            if (is_file($filePath)) {
                $files[] = $filePath;
            }
        }

        return new \ArrayIterator($files);
    }
}
