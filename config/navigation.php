<?php

declare(strict_types=1);

return [
    'breadcrumbs' => [
        'models' => [
            'lawn' => \App\Models\Lawn::class,
            // Weitere Model-Mappings hier...
        ],
        'segments' => [
            'dashboard' => [
                'dashboard' => [
                    'label' => 'Dashboard',
                    'route' => null,
                ],
            ],
            'profile.index' => [
                'profile' => [
                    'label' => 'Profil',
                    'route' => null,
                ],
            ],
            'profile.edit' => [
                'profile' => [
                    'label' => 'Profil',
                    'route' => 'profile.index',
                ],
                'profile.edit' => [
                    'label' => 'Bearbeiten',
                    'route' => null,
                ],
            ],
            'lawn.index' => [
                'lawn' => [
                    'label' => 'Rasenflächen',
                    'route' => null,
                ],
            ],

            'lawn.create' => [
                'lawn' => [
                    'label' => 'Rasenflächen',
                    'route' => 'lawn.index',
                ],
                'lawn.create' => [
                    'label' => 'create',
                    'route' => null,
                ],
            ],
            'lawn.show' => [
                'lawn' => [
                    'label' => 'Rasenflächen',
                    'route' => 'lawn.index',
                ],
                'lawn.show' => [
                    'label' => ':lawn_name',
                    'route' => null,
                ],
            ],
            'lawn.edit' => [
                'lawn' => [
                    'label' => 'Rasenflächen',
                    'route' => 'lawn.index',
                ],
                'lawn.edit' => [
                    'label' => ':lawn_name',
                    'route' => null,
                ],
            ],
            // weitere Routen...
        ],
    ],
];
