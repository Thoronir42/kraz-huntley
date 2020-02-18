<?php declare(strict_types=1);

namespace App\LeanMapper;

use LeanMapper\DefaultMapper;
use LeanMapper\Exception\InvalidStateException;
use LeanMapper\Row;

/**
 * Taken from https://github.com/LeanMapper/examples/blob/master/underscore-mapper/Mapper.php
 */
class UnderscoreMapper extends DefaultMapper
{

    protected $relationshipTableGlue = '_has_';

    /**
     * UnderscoreMapper constructor.
     * @param string $defaultEntityNamespace
     */
    public function __construct($defaultEntityNamespace = null)
    {
        $this->defaultEntityNamespace = $defaultEntityNamespace;
    }

    public function getTable($entityClass)
    {
        return self::toUnderScore($this->trimNamespace($entityClass));
    }

    public function getEntityClass($table, Row $row = null)
    {
        return ($this->defaultEntityNamespace !== null ? $this->defaultEntityNamespace . '\\' : '')
            . ucfirst(self::toCamelCase($table));
    }

    public function getColumn($entityClass, $field)
    {
        return self::toUnderScore($field);
    }

    public function getEntityField($table, $column)
    {
        return self::toCamelCase($column);
    }

    public function getTableByRepositoryClass($repositoryClass)
    {
        $matches = array();
        if (preg_match('#([a-z0-9]+)repository$#i', $repositoryClass, $matches)) {
            return self::toUnderScore($matches[1]);
        }
        throw new InvalidStateException('Cannot determine table name.');
    }

    public static function toUnderScore($str)
    {
        return lcfirst(preg_replace_callback('#(?<=.)([A-Z])#', function ($m) {
            return '_' . strtolower($m[1]);
        }, $str));
    }

    public static function toCamelCase($str)
    {
        return preg_replace_callback('#_(.)#', function ($m) {
            return strtoupper($m[1]);
        }, $str);
    }
}
