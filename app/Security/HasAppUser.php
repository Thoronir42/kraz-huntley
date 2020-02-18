<?php declare(strict_types=1);

namespace App\Security;


use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use Nette\Security\User as SecurityUser;

/**
 * Trait HasAppUser
 *
 * @property-read SecurityUser $user
 */
trait HasAppUser
{
    /** @var User */
    protected $appUser;

    public function injectAppUser(UserRepository $userRepository)
    {
        if ($this->user->isLoggedIn()) {
            $this->appUser = $userRepository->findOneBy(['id' => $this->user->id]);
        }
    }
}
