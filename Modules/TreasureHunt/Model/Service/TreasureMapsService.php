<?php

namespace CP\TreasureHunt\Model\Service;


use CP\TreasureHunt\Model\Entity\TreasureMap;
use League\Flysystem\Filesystem;
use Nette\InvalidStateException;
use Nette\Utils\Image;

class TreasureMapsService
{
    private $maps;
    /** @var Filesystem */
    private $sources;
    /** @var Filesystem */
    private $destination;

    public function __construct(Filesystem $sources, Filesystem $destination, array $maps)
    {
        $this->sources = $sources;
        $this->destination = $destination;
        $this->maps = $maps;
    }

    public function getMap(string $id): ?TreasureMap
    {
        $mapData = $this->maps[$id] ?? null;
        if (!$mapData) {
            return null;
        }

        $sourceFilename = $mapData['sourceFilename'];
        if (!$this->sources->fileExists($sourceFilename)) {
            throw new InvalidStateException("Invalid filename for map '$id'");
        }

        $image = Image::fromString($this->sources->read($sourceFilename));

        $map = new TreasureMap();
        $map->width = $image->width;
        $map->height = $image->height;

        $map->tilingX = $mapData['tilingX'];
        $map->tilingY = $mapData['tilingY'];
        $files = $this->chopMap($image, $id, $map->width, $map->height);

        $map->files = $files;

        return $map;
    }

    private function chopMap(Image $sourceImage, string $outSubdirectory, int $width, int $height): array
    {
        $tileWidth = (int)($sourceImage->width / $width);
        $tileHeight = (int)($sourceImage->height / $height);

        $files = $this->destination->listContents($outSubdirectory)
            ->map(function ($item) {
                return $item->path();
            })
            ->toArray();

        if ($files) {
            return $files;
        }

        $files = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $destFilename = $outSubdirectory
                    . "/" . mb_substr(md5("$outSubdirectory.$x.$y"), 0, 6)
                    . image_type_to_extension(Image::JPEG);

                $destImage = Image::fromBlank($tileWidth, $tileHeight);

                $srcX = $x * $tileWidth;
                $srcY = $y * $tileHeight;

                $destImage->copy($sourceImage, 0, 0, $srcX, $srcY, $tileWidth, $tileHeight);


                $this->destination->write($destFilename, $destImage->toString());
                $files[] = $destFilename;
            }
        }

        return $files;
    }
}
