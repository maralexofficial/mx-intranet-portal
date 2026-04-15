<?php
$path = __DIR__;
$items = scandir($path);

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