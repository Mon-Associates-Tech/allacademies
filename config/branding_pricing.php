<?php

declare(strict_types=1);

return [
    'currency' => 'GHS',
    'plans' => [
        'basic' => [
            'title' => 'Basic Subscription',
            'subtitle' => 'For Individuals in Basic Schools',
            'cta' => 'Get Started',
            'options' => [
                'quarterly' => [
                    'label' => 'Quarterly Subscription',
                    'price' => 20,
                    'period' => 'Per Subject For 3 Months',
                    'badge' => 'SAVE 15%',
                    'badge_visible' => false,
                ],
                'biannual' => [
                    'label' => 'Biannual Subscription',
                    'price' => 30,
                    'period' => 'Per Subject For 6 Months',
                    'badge' => 'SAVE 15%',
                    'badge_visible' => false,
                ],
                'annual' => [
                    'label' => 'Annual Subscription',
                    'price' => 45,
                    'period' => 'Per Subject For 1 Year',
                    'badge' => 'BEST VALUE',
                    'badge_visible' => false,
                ],
            ],
        ],
        'secondary' => [
            'title' => 'Secondary Subscription',
            'subtitle' => 'For Individuals in Senior High Schools',
            'cta' => 'Subscribe Now',
            'options' => [
                'quarterly' => [
                    'label' => 'Quarterly Subscription',
                    'price' => 35,
                    'period' => 'Per Subject For 3 Months',
                    'badge' => 'SAVE 15%',
                    'badge_visible' => false,
                ],
                'biannual' => [
                    'label' => 'Biannual Subscription',
                    'price' => 50,
                    'period' => 'Per Subject For 6 Months',
                    'badge' => 'SAVE 15%',
                    'badge_visible' => false,
                ],
                'annual' => [
                    'label' => 'Annual Subscription',
                    'price' => 75,
                    'period' => 'Per Subject For 1 Year',
                    'badge' => 'BEST VALUE',
                    'badge_visible' => false,
                ],
            ],
        ],
        'institutional' => [
            'title' => 'Institutional Subscription',
            'subtitle' => 'Subscription covers all subjects per student',
            'cta' => 'Get Started',
            'options' => [
                'quarterly' => [
                    'label' => 'Quarterly Subscription',
                    'badge' => 'SAVE 15%',
                    'badge_visible' => false,
                    'tiers' => [
                        'basic' => [
                            'label' => 'Basic',
                            'price' => 150,
                            'suffix' => '/ Student',
                        ],
                        'secondary' => [
                            'label' => 'Secondary',
                            'price' => 75,
                            'suffix' => '/ Student',
                        ],
                    ],
                ],
                'basic_annual' => [
                    'label' => 'Basic Subscription',
                    'price' => 150,
                    'period' => 'Per Student For 1 Year',
                    'badge' => 'SAVE 15%',
                    'badge_visible' => false,
                ],
                'secondary_annual' => [
                    'label' => 'Secondary Subscription',
                    'price' => 200,
                    'period' => 'Per Student For 1 Year',
                    'badge' => 'BEST VALUE',
                    'badge_visible' => false,
                ],
            ],
        ],
    ],
];
