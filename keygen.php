<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1a202c;
            color: white;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-start pt-10">

    <div class="w-full max-w-lg bg-gray-800 p-5 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center mb-5">ADMIN PANEL</h1>
        <p class="text-center text-sm mb-5">DESIGN BY ENZO</p>

        <form action="generate-key.php" method="POST" class="space-y-4">
            <div>
                <label class="text-sm">Enter manual key</label>
                <input type="text" name="manual_key" placeholder="Enter manual key" 
                       class="w-full p-2 mt-1 rounded bg-gray-700 text-white">
            </div>
            <div>
                <label class="text-sm">Duration (days)</label>
                <input type="number" name="duration" placeholder="Duration (days)" required
                       class="w-full p-2 mt-1 rounded bg-gray-700 text-white">
            </div>
            <button type="submit" 
                    class="w-full bg-green-600 py-2 rounded font-bold hover:bg-green-500 transition">Generate Key</button>
        </form>
    </div>

    <div class="w-full max-w-lg mt-10 bg-gray-800 p-5 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-5">Manage Keys</h2>

        <?php
        $keysFile = './keys.json';
        $keys = file_exists($keysFile) ? json_decode(file_get_contents($keysFile), true) : [];

        if (!empty($keys)) {
            foreach ($keys as $key) {
                $statusColor = $key['status'] === 'active' ? 'text-green-500' : 'text-red-500';
                echo "
                <div class='bg-gray-700 p-4 rounded-lg mb-3'>
                    <p><strong>ID:</strong> {$key['id']}</p>
                    <p><strong>Key:</strong> {$key['key']}</p>
                    <p><strong>Status:</strong> <span class='$statusColor'>{$key['status']}</span></p>
                    <p><strong>Device ID:</strong> {$key['device_id']}</p>
                    <p><strong>Expire:</strong> {$key['expires']}</p>
                    <div class='flex space-x-2 mt-2'>
                        <form action='delete-key.php' method='POST'>
                            <input type='hidden' name='id' value='{$key['id']}'>
                            <button type='submit' class='text-red-500'>Delete</button>
                        </form>
                        <form action='deactivate-key.php' method='POST'>
                            <input type='hidden' name='id' value='{$key['id']}'>
                            <button type='submit' class='text-blue-500'>Deactivate</button>
                        </form>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<p class='text-gray-500'>No keys available</p>";
        }
        ?>
    </div>
</body>
</html>
