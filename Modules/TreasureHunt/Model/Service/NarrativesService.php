<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Service;

use CP\TreasureHunt\Model\Entity\Narrative;
use CP\TreasureHunt\Model\Repository\NarrativeRepository;
use Ublaboo\DataGrid\DataSource\IDataSource;

class NarrativesService
{
    /** @var NarrativeRepository */
    private $narrativeRepository;

    public function __construct(NarrativeRepository $narrativeRepository)
    {
        $this->narrativeRepository = $narrativeRepository;
    }

    public function getNarrativesDataSource(): IDataSource
    {
        return $this->narrativeRepository->getEntityDataSource();
    }

    public function getNarrative(string $narrativeId): ?Narrative
    {
        return $this->narrativeRepository->find($narrativeId);
    }

    public function save(Narrative $narrative)
    {
        $this->narrativeRepository->persist($narrative);
    }
}
