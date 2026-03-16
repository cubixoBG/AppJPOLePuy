<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'nom'          => self::faker()->lastName(),
            'departement'  => self::faker()->randomElement(['Informatique', 'Mathématiques', 'Physique', 'Chimie', 'Lettres']),
            'etablissement'=> self::faker()->company(), 
            'mail'         => self::faker()->unique()->safeEmail(),
            'mdp'          => password_hash('password', PASSWORD_BCRYPT), // ou self::faker()->password()
            'prenom'       => self::faker()->firstName(),
            'tel'          => self::faker()->phoneNumber(),
            'id_journee'   => JourneeFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
