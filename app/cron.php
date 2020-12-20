<?php

require __DIR__ . '/functions.php';

$patrons = getPatreonStatus('5872382');

if ($patrons === null) {
    fetchNewToken();

    $patrons = getPatreonStatus('5872382');
}

$filtered = array_filter($patrons, static function ($patron) {
    return $patron->attributes->patron_status === 'active_patron' &&
        // check if they pay more or equal than $25
        $patron->attributes->will_pay_amount_cents >= 2500;
});
$mapped = array_map(static function($patron) {
    return $patron->attributes->full_name;
}, $filtered);

sort($mapped);

// Store the file
$curWeekNum = date('W');

$path = __DIR__ . "/../storage/patreon-week-$curWeekNum.json";

file_put_contents($path, json_encode($mapped));

$lastWeekNum = $curWeekNum - 1;

if ($lastWeekNum < 1) {
    $lastWeekNum = 1;
}

$oldPath = __DIR__ . "/../storage/patreon-week-$lastWeekNum.json";

if (file_exists($oldPath)) {
    unlink($oldPath);
}
