<?php declare(strict_types=1);

namespace CP\TreasureHunt\Typeful\Types;


use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use SeStep\Typeful\Types\PropertyType;
use SeStep\Typeful\Validation\ValidationError;

class PictureSelection implements PropertyType
{
    /** @var int */
    private $slots;
    /** @var Filesystem */
    private $pictureStorage;
    /** @var array */
    private $pictures;
    /** @var string */
    private $baseDirectory;

    public function __construct(int $slots, Filesystem $pictureStorage, string $baseDirectory, array $pictures = null)
    {
        $this->slots = $slots;
        $this->pictureStorage = $pictureStorage;
        $this->baseDirectory = $baseDirectory;
        $this->pictures = $pictures ?: self::getPicturesFromDirectory($pictureStorage);
    }

    public function renderValue($value, array $options = [])
    {
        return implode(', ', $value);
    }

    public function validateValue($value, array $options = []): ?ValidationError
    {
        $invalidIndexes = [];
        foreach ($value as $i => $selection) {
            if (!isset($options['pictures'][$selection])) {
                $invalidIndexes[] = $i;
            }
        }

        if (!empty($invalidIndexes)) {
            return new ValidationError(ValidationError::INVALID_VALUE);
        }

        return null;
    }

    public static function getPicturesFromDirectory(Filesystem $filesystem)
    {
        $pictures = [];

        foreach ($filesystem->listContents('.') as $key => $file) {
            if (!($file instanceof FileAttributes)) {
                continue;
            }
            $fileName = pathinfo($file->path(), PATHINFO_FILENAME);
            $pictures[$fileName] = pathinfo($file->path(), PATHINFO_BASENAME);
        }

        return $pictures;
    }

    public function getSlots(): int
    {
        return $this->slots;
    }

    public function getPictures(): array
    {
        return $this->pictures;
    }

    /** @return string */
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
}
