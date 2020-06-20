<?php

namespace CP\TreasureHunt\Model\Service;

use CP\TreasureHunt\Model\Entity\Attributes\TreasureMapFileAttributes;
use CP\TreasureHunt\Model\Entity\TreasureMap;
use CP\TreasureHunt\Model\Repository\TreasureMapRepository;
use League\Flysystem\Filesystem;
use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Utils\Image;
use Ublaboo\DataGrid\DataSource\IDataSource;

class TreasureMapsService
{
    /** @var TreasureMapRepository */
    private $treasureMapRepository;
    /** @var Filesystem */
    private $sources;
    /** @var Filesystem */
    private $destination;

    public function __construct(
        Filesystem $sources,
        Filesystem $destination,
        TreasureMapRepository $treasureMapRepository
    ) {
        $this->sources = $sources;
        $this->destination = $destination;
        $this->treasureMapRepository = $treasureMapRepository;
    }

    public function getMap(string $id, bool $shuffleFiles = false): ?TreasureMap
    {
        $map = $this->treasureMapRepository->find($id);
        if (!$map) {
            return null;
        }

        if (!$this->sources->fileExists($map->file)) {
            throw new InvalidStateException("Invalid filename for map '$id'");
        }

        $map->fileAttributes = $this->initializeFileAttributes($map, $shuffleFiles);

        return $map;
    }

    public function getDataSource(): IDataSource
    {
        return $this->treasureMapRepository->getEntityDataSource();
    }

    /**
     * @param array|\ArrayAccess $values
     *
     * @return TreasureMap
     */
    public function create($values)
    {
        $values['file'] = $this->saveMapFile($values['file']);

        $map = new TreasureMap($values);
        $this->treasureMapRepository->persist($map);

        return $map;
    }

    public function update(TreasureMap $map, $values)
    {
        if (isset($values['id'])) {
            unset($values['id']);
        }

        if (isset($values['file'])) {
            if ($file = $this->saveMapFile($map->id, $values['file'])) {
                $values['file'] = $file;
            } else {
                unset($values['file']);
            }
        }

        $map->assign($values);
        $this->treasureMapRepository->persist($map);
    }

    private function saveMapFile(string $id, FileUpload $file): ?string
    {
        if (!$file->hasFile()) {
            return null;
        }
        if (!$file->isOk()) {
            throw new InvalidStateException("File upload failed");
        }

        $extension = pathinfo($file->getSanitizedName(), PATHINFO_EXTENSION);
        $destFilename = "$id.$extension";
        $this->sources->write($destFilename, $file->getContents());
        $this->destination->deleteDirectory($id);

        return $destFilename;
    }

    private function initializeFileAttributes(TreasureMap $map, bool $shuffleFiles): TreasureMapFileAttributes
    {
        $image = Image::fromString($this->sources->read($map->file));

        $mapFileAttributes = new TreasureMapFileAttributes();

        $mapFileAttributes->width = $image->width;
        $mapFileAttributes->height = $image->height;
        $mapFileAttributes->pieceFiles = $this->chopMap($image, $map->id, $map->tilingX, $map->tilingY);
        $hash = md5($this->sources->lastModified($map->file) . "-{$map->tilingX}-{$map->tilingY}");
        $mapFileAttributes->version = substr($hash, 0, 6);

        if ($shuffleFiles) {
            shuffle($mapFileAttributes->pieceFiles);
        }

        return $mapFileAttributes;
    }

    private function chopMap(Image $sourceImage, string $outSubdirectory, int $cols, int $rows): array
    {
        if ($cols > 50 || $rows > 50) {
            throw new InvalidArgumentException("Unsupported tiling $cols*$rows");
        }

        $tileWidth = (int)($sourceImage->width / $cols);
        $tileHeight = (int)($sourceImage->height / $rows);

        $files = $this->destination->listContents($outSubdirectory)
            ->map(function ($item) {
                return $item->path();
            })
            ->toArray();

        if ($files) {
            if (count($files) === $rows * $cols) {
                return $files;
            }

            $this->destination->deleteDirectory($outSubdirectory);
        }

        $files = [];
        for ($y = 0; $y < $rows; $y++) {
            for ($x = 0; $x < $cols; $x++) {
                $destFilename = $outSubdirectory
                    . "/" . mb_substr(md5("$outSubdirectory.$x/$cols.$y/$rows"), 0, 6)
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
