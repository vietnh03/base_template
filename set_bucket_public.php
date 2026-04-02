<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Dotenv\Dotenv;

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$bucket = $_ENV['AWS_BUCKET'] ?? 'uploads';
$endpoint = $_ENV['AWS_ENDPOINT'] ?? 'http://minio:9000';

// If running from host, 'minio' might not resolve, use 127.0.0.1 or the IP
// But since this is likely running in the same environment as the app:
// Let's check if we can reach 'minio:9000'
$host = parse_url($endpoint, PHP_URL_HOST);
if ($host === 'minio' && gethostbyname($host) === $host) {
    // Cannot resolve 'minio', fallback to localhost if port 9000 is open
    $endpoint = str_replace('minio', '127.0.0.1', $endpoint);
}

echo "Connecting to MinIO at $endpoint...\n";

$client = new S3Client([
    'version' => 'latest',
    'region' => $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1',
    'endpoint' => $endpoint,
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? 'minio',
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '12345678',
    ],
]);

$policy = '{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Principal": {"AWS": ["*"]},
            "Action": ["s3:GetBucketLocation", "s3:ListBucket"],
            "Resource": ["arn:aws:s3:::' . $bucket . '"]
        },
        {
            "Effect": "Allow",
            "Principal": {"AWS": ["*"]},
            "Action": ["s3:GetObject"],
            "Resource": ["arn:aws:s3:::' . $bucket . '/*"]
        }
    ]
}';

try {
    echo "Setting policy for bucket: $bucket\n";
    $client->putBucketPolicy([
        'Bucket' => $bucket,
        'Policy' => $policy,
    ]);
    echo "Success: Bucket '$bucket' is now public.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTip: If you are running this from outside Docker, make sure port 9000 is mapped to your host and use 'http://127.0.0.1:9000'.\n";
}
