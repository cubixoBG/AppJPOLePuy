<?php

namespace App\Factory;

use App\Entity\Departement;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Departement>
 */
final class DepartementFactory extends PersistentProxyObjectFactory
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
        return Departement::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {

        $name = [
            'Informatique',
            'GEA',
            'TC',
            'GMP',
            'GEII',
            'MP',
            'Chimie',
            'MMI',
        ];

        $descriptions = [
            'Formation axée sur les compétences techniques et professionnelles.',
            'Département formant aux métiers du management et de la gestion.',
            'Département spécialisé dans les technologies industrielles.',
            'Formation professionnalisante en lien avec les entreprises locales.',
            'Département orienté vers l’innovation technologique.',
        ];

        $emojis = ['💻','📊','⚙️','📡','🧪','🏗️'];

        return [
            'description' => self::faker()->randomElement($descriptions),
            'nom' => self::faker()->randomElement($name),
            'nom_responsable' => self::faker()->name(),
            'logo' => self::faker()->randomElement($emojis),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Departement $departement): void {})
        ;
    }
}
