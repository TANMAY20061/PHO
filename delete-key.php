<?php
$keysFile = './keys.json';
$keys = file_exists($keysFile) ? json_decode(file_get_contents($keysFile), true) : [];

$id = $_POST['id'] ?? null;

if ($id !== null) {
    $keys = array_filter($keys, fn($key) => $key['id'] !== (int)$id);
    file_put_contents($keysFile, json_encode(array_values($keys), JSON_PRETTY_PRINT));
}

header('Location: admin-panel.php');
?>
