<?php

$configPath = dirname(__DIR__) . '/config.json';
$config = json_decode(file_get_contents($configPath), true);

$baseDir = __DIR__ . '/uploads';
$urlBase = '/files/uploads';

$currentDir = $_GET['dir'] ?? '';

// security: prevent path traversal
$realBase = realpath($baseDir);
$currentPath = realpath($baseDir . '/' . $currentDir);

if (!$currentPath || strpos($currentPath, $realBase) !== 0) {
    $currentPath = $realBase;
    $currentDir = '';
}

$dirExists = is_dir($currentPath);
$allItems = $dirExists ? scandir($currentPath) : [];

function formatSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;

    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 1) . ' ' . $units[$i];
}

function isAllowed($item, $path, $config)
{
    $fullPath = $path . '/' . $item;

    if (empty($config['show']['hidden']) && $item[0] === '.') {
        return false;
    }

    if (!empty($config['deny_files']) && in_array($item, $config['deny_files'])) {
        return false;
    }

    $isDir = is_dir($fullPath);
    $ext = pathinfo($item, PATHINFO_EXTENSION);

    if ($isDir && empty($config['show']['directories'])) {
        return false;
    }

    if (!$isDir && empty($config['show']['files'])) {
        return false;
    }

    if (!empty($config['deny_extensions']) && in_array($ext, $config['deny_extensions'])) {
        return false;
    }

    if (!empty($config['allow_extensions'])) {
        if ($isDir)
            return true;
        return in_array($ext, $config['allow_extensions']);
    }

    return true;
}

$items = $dirExists
    ? array_values(array_filter($allItems, function ($item) use ($currentPath, $config) {
        return isAllowed($item, $currentPath, $config);
    }))
    : [];

sort($items);

function buildPath($currentDir, $item)
{
    return trim($currentDir . '/' . $item, '/');
}

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MX INTRANET | FILES</title>
    <link rel="stylesheet" href="/assets/css/tailwind.build.css">

    <script>
async function uploadFile(file) {
    if (!file) return;

    const formData = new FormData();
    formData.append("file", file);

    try {
        const res = await fetch("/files/upload.php", {
            method: "POST",
            body: formData
        });

        const result = await res.text();
        console.log("Upload result:", result);

        location.reload();

    } catch (err) {
        console.error("Upload failed:", err);
        alert("Upload failed");
    }
}
</script>
</head>

<body class="m-0 font-sans bg-[#0f1117] text-[#e6e6e6]">

<div class="max-w-[900px] mx-auto mt-10 p-5">

    <div class="flex items-center justify-between mb-5">

        <div>
            <h1 class="text-[22px]">📁 File Browser</h1>

            <?php if ($currentDir): ?>
                <a href="?dir=<?= urlencode(dirname($currentDir)) ?>" class="text-orange-400 hover:underline text-sm">
                    ← Back
                </a>
            <?php endif; ?>
        </div>

        <label class="cursor-pointer bg-orange-500 hover:bg-orange-600 text-black px-4 py-2 rounded-lg text-sm font-semibold transition">
            ⬆ Upload
            <input type="file" class="hidden" onchange="uploadFile(this.files[0])" />
        </label>

    </div>

    <?php if (!$dirExists): ?>
        <div class="mb-5 p-3 rounded-lg bg-red-900/40 border border-red-500 text-red-300">
            ⚠️ Upload directory not found: <?= htmlspecialchars($currentPath) ?>
        </div>
    <?php endif; ?>

    <table class="w-full border-collapse bg-[#161a22] rounded-lg overflow-hidden">

        <thead>
            <tr>
                <th class="p-3 text-left bg-[#1f2430]">Name</th>
                <th class="p-3 text-left bg-[#1f2430]">Size</th>
                <th class="p-3 text-left bg-[#1f2430]">Modified</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!$dirExists): ?>

            <tr>
                <td colspan="3" class="p-4 text-red-400 text-center">
                    ⚠️ Upload directory not available
                </td>
            </tr>

        <?php elseif (empty($items)): ?>

            <tr>
                <td colspan="3" class="p-4 text-gray-400 text-center">
                    📭 No files found in this directory
                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($items as $item): ?>

                <?php
                if ($item === '.' || $item === '..')
                    continue;

                $full = $currentPath . '/' . $item;
                $isDir = is_dir($full);

                $relativePath = buildPath($currentDir, $item);
                $url = $urlBase . '/' . $relativePath;
                ?>

                <tr class="hover:bg-[#222838]">

                    <td class="p-3">
                        <?php if ($isDir): ?>
                            <a href="?dir=<?= urlencode($relativePath) ?>" class="text-[#6ea8fe] hover:underline">
                                📁 <?= htmlspecialchars($item) ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($url) ?>" class="text-[#6ea8fe] hover:underline">
                                📄 <?= htmlspecialchars($item) ?>
                            </a>
                        <?php endif; ?>
                    </td>

                    <td class="p-3 text-[#9aa4b2] text-sm">
                        <?= $isDir ? '-' : formatSize(filesize($full)) ?>
                    </td>

                    <td class="p-3 text-[#9aa4b2] text-sm">
                        <?= date('Y-m-d H:i', filemtime($full)) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</body>
</html>