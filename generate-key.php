<?php
$keysFile = './keys.json';
$keys = file_exists($keysFile) ? json_decode(file_get_contents($keysFile), true) : [];

$manualKey = $_POST['manual_key'] ?? null;
$duration = $_POST['duration'] ?? 0;
$id = count($keys) + 1;

$generatedKey = $manualKey ?: strtoupper(bin2hex(random_bytes(3))); // 6-character random key
$expirationDate = new DateTime();
$expirationDate->modify("+$duration days");

$newKey = [
    'id' => $id,
    'key' => $generatedKey,
    'status' => 'active',
    'device_id' => '',
    'expires' => $expirationDate->format('Y-m-d H:i:s')
];

$keys[] = $newKey;
file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));

header('Location: admin-panel.php');
?>
