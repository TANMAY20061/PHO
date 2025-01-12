<?php
// Key validation logic
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyValue = $_POST['key_value'] ?? '';
    $deviceId = $_POST['device_id'] ?? '';

    $keysFile = './keys.json';
    $keys = file_exists($keysFile) ? json_decode(file_get_contents($keysFile), true) : [];

    // Search for the key in the keys.json file
    $foundKey = null;
    foreach ($keys as &$key) {
        if ($key['key'] === $keyValue) {
            $foundKey = &$key;
            break;
        }
    }

    if ($foundKey) {
        if ($foundKey['status'] !== 'active') {
            $message = ['type' => 'error', 'text' => 'Key is inactive.'];
        } elseif (new DateTime() > new DateTime($foundKey['expires'])) {
            $message = ['type' => 'error', 'text' => 'Key has expired.'];
        } elseif (!empty($foundKey['device_id']) && $foundKey['device_id'] !== $deviceId) {
            $message = ['type' => 'error', 'text' => 'Key is already bound to another device.'];
        } else {
            // Valid key: Update the device_id if not already bound
            if (empty($foundKey['device_id'])) {
                $foundKey['device_id'] = $deviceId;
                file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));
            }
            $message = ['type' => 'success', 'text' => 'Key validated successfully!'];
        }
    } else {
        $message = ['type' => 'error', 'text' => 'Invalid key.'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Key Validation</title>
    <style>
        body {
            background: url('bg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'El Messiri', sans-serif;
        }
        .hover-secondary:hover {
            color: #1e7e34; /* Darker green on hover */
        }
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center">
    <div class="bg-white/10 backdrop-blur-md shadow-lg rounded-lg p-8 w-full max-w-sm border border-blue-300/50">
        <div class="text-center mb-6">
            <img src="royal.jpg" alt="Logo" class="mx-auto w-24 h-24 rounded-full border-4 border-green-500" />
            <h1 class="text-2xl font-bold mt-2 text-white">TANMAY SERVER</h1> 
        </div>

        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded <?= $message['type'] === 'success' ? 'bg-green-500' : 'bg-red-500'; ?> text-white text-center">
                <?= $message['text']; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4 relative">
                <i class="fas fa-key absolute left-3 top-1/2 transform -translate-y-1/2 text-green-500"></i> <!-- Icon in green -->
                <input type="text" name="key_value" placeholder="Enter Key" required 
                       class="border border-blue-300/50 p-3 pl-10 rounded w-full focus:ring-2 focus:ring-sky-400 outline-none bg-white/20 text-white placeholder-white">
            </div>
            <input type="hidden" name="device_id" id="deviceIdInput">
            <div class="text-center">
                <button type="submit" 
                        class="bg-white/20 backdrop-blur-md hover:bg-white/30 text-white font-bold py-2 px-4 rounded-full shadow-md flex items-center justify-center w-full transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2 text-green-700"></i> Login
                </button>
            </div>
        </form>

        <div class="mt-4 flex justify-center">
            <button onclick="copyDeviceId()" id="copyButton" class="text-green-700 hover:text-green-800 font-bold flex items-center">
                <i class="fas fa-copy mr-2 text-green-700"></i> Copy Device ID
            </button>
        </div>
    </div>

    <script>
        function generateDeviceId() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let deviceId = '';
            for (let i = 0; i < 6; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                deviceId += characters[randomIndex];
            }
            return deviceId;
        }

        // Retrieve or generate and store the device ID
        let deviceId = localStorage.getItem('device_id');
        if (!deviceId) {
            deviceId = generateDeviceId();
            localStorage.setItem('device_id', deviceId);
        }

        // Set the device ID in the hidden input
        document.getElementById('deviceIdInput').value = deviceId;

        // Copy the device ID to the clipboard
        function copyDeviceId() {
            navigator.clipboard.writeText(deviceId).then(() => {
                // Show a temporary "Copied!" message on the button
                const copyButton = document.getElementById('copyButton');
                copyButton.innerHTML = '<i class="fas fa-check mr-2"></i> Copied!';
                setTimeout(() => {
                    copyButton.innerHTML = '<i class="fas fa-copy mr-2"></i> Copy Device ID';
                }, 2000); // Reset the text after 2 seconds
            });
        }
    </script>
</body>
</html>
