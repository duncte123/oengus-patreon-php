<?php

function getCacheFilePath(): string {
    $curWeekNum = date('W');

    $path = __DIR__ . "/../storage/patreon-week-$curWeekNum.json";

    if (file_exists($path)) {
        return $path;
    }

    $lastWeekNum = $curWeekNum - 1;

    if ($lastWeekNum < 1) {
        $lastWeekNum = 1;
    }

    return __DIR__ . "/../storage/patreon-week-$lastWeekNum.json";
}

$path = getCacheFilePath();

header('Content-Type: application/json', true, 200);

if (!file_exists($path)) {
    echo json_encode([
        '(error, file missing)'
    ]);
}

// the file will contain the parsed array
echo file_get_contents($path);
