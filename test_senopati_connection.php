<?php
/**
 * Test Senopati API Connection
 * Jalankan: php test_senopati_connection.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "=== Test Koneksi Senopati API ===\n\n";

// 1. Cek Environment Variables
echo "1. Environment Variables:\n";
$senopatiUrl = env('SENOPATI_BASE_URL', 'NOT SET');
$senopatiModel = env('SENOPATI_MODEL', 'NOT SET');

echo "   SENOPATI_BASE_URL: {$senopatiUrl}\n";
echo "   SENOPATI_MODEL: {$senopatiModel}\n\n";

if ($senopatiUrl === 'NOT SET') {
    echo "❌ SENOPATI_BASE_URL tidak ter-set!\n";
    echo "   Tambahkan di Railway Variables atau .env file\n\n";
    exit(1);
}

// 2. Test koneksi ke Senopati
echo "2. Testing koneksi ke Senopati API...\n";

$testPayload = [
    'model' => $senopatiModel,
    'prompt' => 'Halo, ini test koneksi. Balas dengan: {"status": "OK", "message": "Koneksi berhasil"}',
    'system' => 'Kamu adalah asisten yang selalu membalas dengan JSON.',
    'stream' => false,
    'options' => [
        'temperature' => 0.1,
        'max_tokens' => 100
    ]
];

$ch = curl_init($senopatiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$duration = round(($endTime - $startTime) * 1000, 2);

echo "   HTTP Code: {$httpCode}\n";
echo "   Duration: {$duration}ms\n";

if ($curlError) {
    echo "   ❌ cURL Error: {$curlError}\n\n";
    exit(1);
}

if ($httpCode !== 200) {
    echo "   ❌ HTTP Error: {$httpCode}\n";
    echo "   Response: {$response}\n\n";
    exit(1);
}

echo "   ✅ Koneksi berhasil!\n";
echo "   Response: " . substr($response, 0, 200) . "...\n\n";

// 3. Test Python environment
echo "3. Testing Python environment...\n";

$pythonCheck = shell_exec('python --version 2>&1');
echo "   Python version: " . trim($pythonCheck) . "\n";

$pypdfCheck = shell_exec('python -c "import pypdfium2; print(\'✓ pypdfium2\')" 2>&1');
echo "   pypdfium2: " . trim($pypdfCheck) . "\n";

$pypdf2Check = shell_exec('python -c "import PyPDF2; print(\'✓ PyPDF2\')" 2>&1');
echo "   PyPDF2: " . trim($pypdf2Check) . "\n";

$requestsCheck = shell_exec('python -c "import requests; print(\'✓ requests\')" 2>&1');
echo "   requests: " . trim($requestsCheck) . "\n\n";

echo "=== Test Selesai ===\n";
echo "✅ Semua test berhasil! Senopati API siap digunakan.\n";
