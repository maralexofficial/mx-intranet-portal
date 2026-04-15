<?php

$configPath = dirname(__DIR__) . '/config.json';
$config = json_decode(file_get_contents($configPath), true);

$path = __DIR__ . '/uploads';

$dirExists = is_dir($path);
$allItems = $dirExists ? scandir($path) : [];

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

    // hidden files
    if (empty($config['show']['hidden']) && $item[0] === '.') {
        return false;
    }

    // deny files
    if (!empty($config['deny_files']) && in_array($item, $config['deny_files'])) {
        return false;
    }

    $isDir = is_dir($fullPath);
    $ext = pathinfo($item, PATHINFO_EXTENSION);

    // directories toggle
    if ($isDir && empty($config['show']['directories'])) {
        return false;
    }

    // files toggle
    if (!$isDir && empty($config['show']['files'])) {
        return false;
    }

    // deny extensions
    if (!empty($config['deny_extensions']) && in_array($ext, $config['deny_extensions'])) {
        return false;
    }

    // allow list
    if (!empty($config['allow_extensions'])) {
        if ($isDir)
            return true;
        return in_array($ext, $config['allow_extensions']);
    }

    return true;
}

$items = $dirExists
    ? array_values(array_filter($allItems, function ($item) use ($path, $config) {
        return isAllowed($item, $path, $config);
    }))
    : [];

sort($items);

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MX INTRANET | FILES</title>

    <link rel="stylesheet" href="/assets/css/tailwind.build.css">
</head>

<body class="m-0 font-sans bg-[#0f1117] text-[#e6e6e6]">

<div class="max-w-[900px] mx-auto mt-10 p-5">

    <h1 class="text-[22px] mb-5">📁 File Browser</h1>

    <?php if (!$dirExists): ?>
        <div class="mb-5 p-3 rounded-lg bg-red-900/40 border border-red-500 text-red-300">
            ⚠️ Upload directory not found: <?= htmlspecialchars($path) ?>
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
                    📭 No files found in uploads directory
                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($items as $item): ?>

                <?php
                if ($item === '.' || $item === '..')
                    continue;

                $full = $path . '/' . $item;
                $isDir = is_dir($full);
                ?>

                <tr class="hover:bg-[#222838]">

                    <td class="p-3">
                        <?php if ($isDir): ?>
                            <a href="<?= $item ?>/" class="text-[#6ea8fe] hover:underline">
                                📁 <?= htmlspecialchars($item) ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= $item ?>" class="text-[#6ea8fe] hover:underline">
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