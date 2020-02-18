<?php declare(strict_types=1);

namespace App\Model\Entity;

use LeanMapper\Entity;

/**
 * Class User
 *
 * @property string $id
 * @property string $nick
 * @property string $pass - Personal Authentication Symbol String (hashed)
 */
class User extends Entity
{

}
