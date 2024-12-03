<?php

// Load ENV
$env = parse_ini_file('create-labels.env');
if ($env === false) {
    die("Error loading .env file\n");
}

// Validate ENV variables
if (!isset($env['GITHUB_TOKEN']) || !isset($env['GITHUB_REPO'])) {
    die("Missing required ENV variables GITHUB_TOKEN or GITHUB_REPO\n");
}

$milestones = [
    [
        'title' => 'MVP - Basic Lawn Management',
        'description' => "Basic features for lawn management:\n- User Authentication\n- Basic Task Management\n- Simple Lawn Profile",
        'due_on' => '2024-01-31T00:00:00Z'
    ],
    [
        'title' => 'Enhanced Task Management',
        'description' => "Advanced task features:\n- Recurring Tasks\n- Task Categories\n- Task Dependencies\n- Notifications",
        'due_on' => '2024-02-29T00:00:00Z'
    ],
    [
        'title' => 'Image & Documentation Features',
        'description' => "Visual and documentation features:\n- Image Upload\n- Progress Tracking\n- Lawn Documentation\n- Task History",
        'due_on' => '2024-03-31T00:00:00Z'
    ],
    [
        'title' => 'Garden Management Extension',
        'description' => "Expand to full garden management:\n- Multiple Areas\n- Plant Database\n- Garden Planning\n- Weather Integration",
        'due_on' => '2024-04-30T00:00:00Z'
    ],
    [
        'title' => 'Analytics & Reporting',
        'description' => "Data analysis features:\n- Usage Statistics\n- Progress Reports\n- Cost Tracking\n- Performance Analytics",
        'due_on' => '2024-05-31T00:00:00Z'
    ]
];

foreach ($milestones as $milestone) {
    $data = json_encode($milestone);

    $ch = curl_init("https://api.github.com/repos/{$env['GITHUB_REPO']}/milestones");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/vnd.github.v3+json',
        'Authorization: token ' . $env['GITHUB_TOKEN'],
        'User-Agent: PHP Script'
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 201) {
        echo "Created milestone: {$milestone['title']}\n";
    } else {
        echo "Error creating milestone {$milestone['title']}: $result\n";
    }

    curl_close($ch);
}
