<?php

namespace App\Factory;

use App\Entity\Cour;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Cour>
 */
final class CourFactory extends PersistentProxyObjectFactory
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
        return Cour::class;
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
            'nom' => self::faker()->text(255),
            'duree' => self::faker()->numberBetween(1, 3) . 'h' . str_pad(self::faker()->numberBetween(0, 59), 2, '0', STR_PAD_LEFT),
            'annee' => self::faker()->randomElement([1, 2, 3]),
            'enseignant' => self::faker()->name()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Cour $cour): void {})
        ;
    }
}
