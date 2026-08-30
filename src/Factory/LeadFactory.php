<?php

namespace App\Factory;

use App\CRM\Enums\LeadStatus;
use App\Entity\Lead;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Lead>
 */
final class LeadFactory extends PersistentObjectFactory
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
        return Lead::class;
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
            'budget' => self::faker()->randomFloat(2, 1000, 1000000),
            'date_start' => self::faker()->dateTimeBetween('-1 year'),
            'date_next_action' => self::faker()->optional()->dateTimeBetween('now', '+3 months'),

            'name' => self::faker()->words(3, true),
            'product' => self::faker()->word(),

            'source' => self::faker()->randomElement([
                'site',
                'phone',
                'advertising',
                'recommendation',
            ]),

            'next_action' => self::faker()->optional()->word(),
            'comment' => self::faker()->optional()->sentence(),

            'status' => self::faker()->randomElement(LeadStatus::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Lead $lead): void {})
        ;
    }
}
