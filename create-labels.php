<?php

declare(strict_types=1);

// Load ENV
$env = parse_ini_file('create-labels.env');
if ($env === false) {
    exit("Error loading .env file\n");
}

// Validate ENV variables
if (! isset($env['GITHUB_TOKEN']) || ! isset($env['GITHUB_REPO'])) {
    exit("Missing required ENV variables GITHUB_TOKEN or GITHUB_REPO\n");
}

$labels = [
    // Priorität
    ['name' => 'priority: urgent 🔥', 'color' => 'FF0000', 'description' => 'Needs immediate attention'],
    ['name' => 'priority: high', 'color' => 'FF4500', 'description' => 'High priority item'],
    ['name' => 'priority: medium', 'color' => 'FFA500', 'description' => 'Medium priority item'],
    ['name' => 'priority: low', 'color' => '90EE90', 'description' => 'Low priority item'],

    // Status
    ['name' => 'status: in progress', 'color' => '0E8A16', 'description' => 'Currently being worked on'],
    ['name' => 'status: blocked', 'color' => 'D93F0B', 'description' => 'Blocked by another issue/PR'],
    ['name' => 'status: review needed', 'color' => 'FBCA04', 'description' => 'Needs review'],
    ['name' => 'status: ready for dev', 'color' => 'BFD4F2', 'description' => 'Ready for development'],

    // Typ
    ['name' => 'type: bug', 'color' => 'd73a4a', 'description' => 'Something isn\'t working'],
    ['name' => 'type: enhancement', 'color' => 'a2eeef', 'description' => 'New feature or request'],
    ['name' => 'type: documentation', 'color' => '0075ca', 'description' => 'Documentation improvements'],
    ['name' => 'type: security', 'color' => 'D93F0B', 'description' => 'Security related issue'],
    ['name' => 'type: dependencies', 'color' => '0366d6', 'description' => 'Dependencies updates'],

    // Bereich
    ['name' => 'area: frontend', 'color' => 'FFE4B5', 'description' => 'Frontend related'],
    ['name' => 'area: backend', 'color' => '87CEEB', 'description' => 'Backend related'],
    ['name' => 'area: database', 'color' => 'DDA0DD', 'description' => 'Database related'],
    ['name' => 'area: testing', 'color' => '98FB98', 'description' => 'Testing related'],
];

// Create labels
foreach ($labels as $label) {
    $data = json_encode([
        'name' => $label['name'],
        'color' => $label['color'],
        'description' => $label['description'],
    ]);

    $ch = curl_init("https://api.github.com/repos/{$env['GITHUB_REPO']}/labels");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/vnd.github.v3+json',
        'Authorization: token '.$env['GITHUB_TOKEN'],
        'User-Agent: PHP Script',
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 201) {
        echo "Created label: {$label['name']}\n";
    } else {
        echo "Error creating label {$label['name']}: $result\n";
    }

    curl_close($ch);
}
