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

list($owner, $repo) = explode('/', $env['GITHUB_REPO']);

// Function to make GraphQL requests with better error handling
function graphqlRequest($query, $token)
{
    $ch = curl_init('https://api.github.com/graphql');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode(['query' => $query]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'User-Agent: PHP Script'
        ]
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "API Response (Status $status):\n";
    echo $response . "\n";

    if ($status !== 200) {
        die("GraphQL request failed\n");
    }

    $decoded = json_decode($response, true);
    if (isset($decoded['errors'])) {
        echo "GraphQL Errors:\n";
        print_r($decoded['errors']);
        die("GraphQL request had errors\n");
    }

    return $decoded;
}

// First, let's verify we can access the API and get basic repo info
echo "Testing API access...\n";
$testQuery = <<<GRAPHQL
query {
  viewer {
    login
  }
  repository(owner: "$owner", name: "$repo") {
    id
    name
  }
}
GRAPHQL;

$result = graphqlRequest($testQuery, $env['GITHUB_TOKEN']);
echo "Connected as: " . $result['data']['viewer']['login'] . "\n";
echo "Repository ID: " . $result['data']['repository']['id'] . "\n";

// Get repository and owner IDs
$queryInfo = <<<GRAPHQL
query {
  repository(owner: "$owner", name: "$repo") {
    id
    owner {
      id
    }
  }
}
GRAPHQL;

echo "\nFetching repository details...\n";
$result = graphqlRequest($queryInfo, $env['GITHUB_TOKEN']);

if (!isset($result['data']['repository'])) {
    die("Could not get repository information\n");
}

$repoId = $result['data']['repository']['id'];
$ownerId = $result['data']['repository']['owner']['id'];

echo "Repository ID: $repoId\n";
echo "Owner ID: $ownerId\n";

// Create project
echo "\nCreating project...\n";
$createProjectMutation = <<<GRAPHQL
mutation {
  createProjectV2(
    input: {
      ownerId: "$ownerId"
      title: "Lawn Management System"
      repositoryId: "$repoId"
    }
  ) {
    projectV2 {
      id
      number
    }
  }
}
GRAPHQL;

$result = graphqlRequest($createProjectMutation, $env['GITHUB_TOKEN']);
