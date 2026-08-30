<?php

namespace App\Factory;

use App\Entity\Client;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Client>
 */
final class ClientFactory extends PersistentObjectFactory
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
        return Client::class;
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
            'name' => self::faker()->company(),
            'inn' => self::faker()->numerify('############'),
            'field_of_activity' => self::faker()->word(),
            'website' => self::faker()->domainName(),
            'phone' => self::faker()->phoneNumber(),
            'email' => self::faker()->safeEmail(),
            'city' => self::faker()->city(),
            'channel' => self::faker()->randomElement([
                'site',
                'phone',
                'recommendation',
                'advertising',
            ]),
            'date_create' => self::faker()->dateTimeBetween('-2 years'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Client $client): void {})
        ;
    }
}
