<?php

$configPath = dirname(__DIR__) . '/config.json';
$config = json_decode(file_get_contents($configPath), true);

$path = __DIR__;
$allItems = scandir($path);

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

$items = array_values(array_filter($allItems, function ($item) use ($path, $config) {
    return isAllowed($item, $path, $config);
}));

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

    <table class="w-full border-collapse bg-[#161a22] rounded-lg overflow-hidden">

        <thead>
            <tr>
                <th class="p-3 text-left bg-[#1f2430]">Name</th>
                <th class="p-3 text-left bg-[#1f2430]">Size</th>
                <th class="p-3 text-left bg-[#1f2430]">Modified</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($items as $item): ?>
            <?php if ($item === '.') continue; ?>
            <?php if ($item === '..') continue; ?>

            <?php
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

        </tbody>

    </table>

</div>

</body>
</html>