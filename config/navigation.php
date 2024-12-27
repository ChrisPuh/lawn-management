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
            'lawn.index' => [
                'lawn' => [
                    'label' => 'Rasenflächen',
                    'route' => null,
                ],
            ],
            'lawn.show' => [
                'lawn' => [
                    'label' => 'Rasenflächen',
                    'route' => 'lawn.index',
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
