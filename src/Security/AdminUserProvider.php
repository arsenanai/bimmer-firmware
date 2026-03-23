<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<InMemoryUser>
 */
class AdminUserProvider implements UserProviderInterface
{
    private string $username;
    private string $plainPassword;
    private UserPasswordHasherInterface $hasher;
    private ?string $cachedHash = null;

    public function __construct(
        string $adminUsername,
        string $adminPassword,
        UserPasswordHasherInterface $hasher,
    ) {
        $this->username = $adminUsername;
        $this->plainPassword = $adminPassword;
        $this->hasher = $hasher;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if ($identifier !== $this->username) {
            throw new UserNotFoundException();
        }

        if (null === $this->cachedHash) {
            $tempUser = new InMemoryUser($this->username, null, ['ROLE_ADMIN']);
            $this->cachedHash = $this->hasher->hashPassword($tempUser, $this->plainPassword);
        }

        return new InMemoryUser($this->username, $this->cachedHash, ['ROLE_ADMIN']);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return InMemoryUser::class === $class;
    }
}
