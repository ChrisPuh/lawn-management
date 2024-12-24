<?php

declare(strict_types=1);

return [
    'breadcrumbs' => [
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
            'lawns.index' => [
                'lawns' => [
                    'label' => 'Rasenflächen',
                    'route' => null,
                ],
            ],
            'lawns.show' => [
                'lawns' => [
                    'label' => 'Rasenflächen',
                    'route' => 'lawns.index',
                ],
                'lawn' => [
                    'label' => ':lawn_name',
                    'route' => null,
                ],
            ],
            // weitere Routen...
        ],
    ],
];
