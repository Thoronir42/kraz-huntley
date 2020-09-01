<?php declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\LeanMapper\Repository;

/**
 * Class UserRepository
 *
 * @method User findOneBy(array $conditions)
 * @method User[] findBy(array $conditions, array $order = [], int $limit = null, int $offset = null)
 */
class UserRepository extends Repository
{
    public function findByNick(string $nick): ?User
    {
        return $this->findOneBy([
            'nick' => $nick,
        ]);
    }
}
