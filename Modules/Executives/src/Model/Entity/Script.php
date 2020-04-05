<?php declare(strict_types=1);

namespace SeStep\Executives\Model\Entity;

use LeanMapper\Entity;

/**
 * @property string $id
 * @property string $method m:enum(self::METHOD_*)
 *
 * @property Action[] $actions m:belongsToMany(script_id)
 */
class Script extends Entity
{
    const METHOD_STOP_ON_FIRST_PASS = 'firstPass';
    const METHOD_STOP_ON_FIRST_FAIL = 'firstFail';
    const METHOD_EXECUTE_ALL = 'executeAll';
}
