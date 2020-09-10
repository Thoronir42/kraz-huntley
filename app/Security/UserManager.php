<?php declare(strict_types=1);

namespace App\Security;


use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class UserManager implements IAuthenticator
{
    const ROLE_POWER_USER = 'powerUser';

    /** @var Passwords */
    private $passwords;
    /** @var UserRepository */
    private $userRepository;

    /** @var string[] */
    private $powerUsers = [];

    public function __construct(UserRepository $userRepository, Passwords $passwords)
    {
        $this->userRepository = $userRepository;
        $this->passwords = $passwords;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(array $credentials): IIdentity
    {
        [$nick, $password] = $credentials;

        $user = $this->userRepository->findByNick($nick);
        if (!$user) {
            $this->passwords->hash($password);
            throw new AuthenticationException('auth.userNotFound', self::IDENTITY_NOT_FOUND);
        }
        if (!$this->passwords->verify($password, $user->pass)) {
            throw new AuthenticationException('auth.invalidCredentials', self::INVALID_CREDENTIAL);
        }

        $roles = [];
        if (in_array($nick, $this->powerUsers)) {
            $roles[] = self::ROLE_POWER_USER;
        }

        return new Identity($user->id, $roles, [
            'nick' => $nick,
        ]);
    }

    public function register(string $nick, string $password)
    {
        if ($this->userRepository->findBy(['nick' => $nick])) {
            return false;
        }

        $user = new User();
        $user->nick = $nick;
        $user->pass = $this->passwords->hash($password);
        $this->userRepository->persist($user);

        return true;
    }

    public function updatePassword(User $user, string $pass)
    {
        $user->pass = $this->passwords->hash($pass);
        $this->userRepository->persist($user);
    }
}
