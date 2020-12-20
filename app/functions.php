<?php

// get campaigns https://www.patreon.com/api/oauth2/v2/campaigns

function getTokenFile(): object {
    $path = __DIR__ . "/../storage/patreon_token_oengus.json";

    if (!file_exists($path)) {
        throw new InvalidArgumentException('Token file is missing.');
    }

    return json_decode(file_get_contents($path));
}

function fetchNewToken() {
    $token = getTokenFile();
    $env = require __DIR__ . '/env.php';

    $ch = curl_init();

    $url = 'https://www.patreon.com/api/oauth2/token?'.
        // patron info
        'grant_type=refresh_token'.
        // max records
        '&refresh_token='.$token->refresh_token.
        '&client_id='.$env['client_id'].
        '&client_secret='.$env['client_secret'];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PatreonFetcher/duncte123.me');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    file_put_contents(__DIR__ . "/../storage/patreon_token_oengus.json", $result);
}

function getPatreonStatus(string $campaignId): ?array {
    $ch = curl_init();

    $url = "https://www.patreon.com/api/oauth2/v2/campaigns/$campaignId/members?".
        // patron info
        'fields%5Bmember%5D=full_name,patron_status,will_pay_amount_cents'.
        // max records
        '&page%5Bcount%5D=1000';

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PatreonFetcher/duncte123.me');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . getTokenFile()->access_token,
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    if ($result === false) {
        return null;
    }

    return json_decode($result)->data;
}

function parseResponse(string $response): array {
    return [];
}
