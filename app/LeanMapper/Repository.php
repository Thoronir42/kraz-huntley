<?php declare(strict_types=1);

namespace App\LeanMapper;

use Dibi\Fluent;
use Dibi\UniqueConstraintViolationException;
use LeanMapper\Entity;
use SeStep\EntityIds\IdGenerator;

class Repository extends \LeanMapper\Repository implements IQueryable
{
    private const MAX_ID_ATTEMPTS = 10;

    /** @var LeanQueryFilter */
    protected $filter;

    /** @var IdGenerator */
    protected $idGenerator;

    /**
     * @param LeanQueryFilter $filter
     */
    public function injectFilter(LeanQueryFilter $filter): void
    {
        $this->filter = $filter;
    }

    public function find($primaryKeyValue)
    {
        $index = $this->mapper->getPrimaryKey($this->getTable());
        $criteria = [$index => $primaryKeyValue];

        if (is_array($primaryKeyValue)) {
            return $this->findBy($criteria);
        } else {
            return $this->findOneBy($criteria);
        }
    }

    public function findBy(array $conditions, array $order = [], int $limit = null, int $offset = null)
    {
        /** @var Fluent $fluent */
        $fluent = $this->select();

        $this->filter->apply($fluent, $conditions, $this->mapper->getEntityClass($this->getTable()));

        if (is_integer($limit)) {
            $fluent->limit($limit);
        }

        if (is_integer($offset)) {
            $fluent->offset($offset);
        }

        if ($order) {
            $fluent->orderBy($order);
        }

        return $this->createEntities($fluent->fetchAll());
    }

    public function findOneBy(array $conditions)
    {
        /** @var Fluent $fluent */
        $fluent = $this->select();

        $this->filter->apply($fluent, $conditions, $this->mapper->getEntityClass($this->getTable()));

        if ($row = $fluent->fetch()) {
            return $this->createentity($row);
        }

        return null;
    }

    protected function select(?string $tableAlias = 't'): Fluent
    {
        $select = $tableAlias ? "$tableAlias.*" : '*';
        return $this->connection->select($select)->from($this->getTable() . ($tableAlias ? " AS $tableAlias" : ''));
    }

    /**
     * Sets given idGenerator and initializes events
     *
     * @param IdGenerator $generator
     */
    public function bindIdGenerator(IdGenerator $generator)
    {
        if ($this->idGenerator) {
            throw new \RuntimeException("Id generator already set");
        }

        $this->idGenerator = $generator;

        $this->events->registerCallback($this->events::EVENT_BEFORE_CREATE, [$this, 'assignId']);
        $this->events->registerCallback($this->events::EVENT_BEFORE_UPDATE, [$this, 'validateAssignedId']);
    }


    public function assignId(Entity $entity)
    {
        $type = get_class($entity);
        $primary = $this->mapper->getPrimaryKey($this->mapper->getTable($type));

        if (!isset($entity->$primary) || !$entity->$primary) {
            $entity->$primary = $this->getUniqueId($type);
        }
    }

    public function validateAssignedId(Entity $entity)
    {
        $type = get_class($entity);
        $primary = $this->mapper->getPrimaryKey($this->mapper->getTable($type));

        $changed = $entity->getModifiedRowData();
        if (!array_key_exists($primary, $changed)) {
            return;
        }
        if ($this->idGenerator->getType($changed[$primary]) !== $type) {
            throw new \UnexpectedValueException("Id '{$changed[$primary]}' could not be validated for type '$type'");
        }
    }

    private function getUniqueId(string $type = null)
    {
        $i = 0;
        do {
            if (++$i > self::MAX_ID_ATTEMPTS) {
                throw new UniqueConstraintViolationException("Could not get an unique ID after "
                    . self::MAX_ID_ATTEMPTS . ' attempts');
            }
            $id = $this->idGenerator->generateId($type);
        } while ($this->find($id));

        return $id;
    }

    protected function getEntityClass(): ?string
    {
        return $this->mapper->getEntityClass($this->getTable());
    }

    public function getDataSource(string $alias = null): Fluent
    {
        return $this->select($alias);
    }

    public function getEntityDataSource(array $conditions = null): LeanMapperDataSource
    {
        $entityClass = $this->mapper->getEntityClass($this->getTable());

        $fluent = $this->connection->command();
        $fluent
            ->select('t.*')
            ->from($this->getTable() . ' AS t');

        if ($conditions) {
            $this->filter->apply($fluent, $conditions, $entityClass);
        }

        return new LeanMapperDataSource($fluent, $this, $this->mapper, $entityClass);
    }

    public function makeEntity($row)
    {
        if (!$row) {
            return null;
        }

        return $this->createEntity($row);
    }

    public function makeEntities(array $rows): array
    {
        return $this->createEntities($rows);
    }

    protected function getNextInSequence(string $field, array $conditions, string $entityClass)
    {
        $column = $this->mapper->getColumn($entityClass, $field);

        $query = $this->connection->select('IFNULL(MAX(%n), 0)', $column)->from($this->getTable());
        $this->filter->apply($query, $conditions, $entityClass);

        return $query->fetchSingle() + 1;
    }

    public function persistMany(array $entities)
    {
        foreach ($entities as $entity) {
            $this->persist($entity);
        }
    }
}
