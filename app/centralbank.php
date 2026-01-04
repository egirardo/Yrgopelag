<?php

declare(strict_types=1);

function centralbankPost(string $endpoint, array $payload): array
{
    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($payload),
            'ignore_errors' => true
        ]
    ]);

    $response = file_get_contents(
        CENTRALBANK_BASE . $endpoint,
        false,
        $context
    );

    if ($response === false) {
        throw new Exception('Centralbank request failed');
    }

    if (empty($response)) {
        throw new Exception('Centralbank returned empty response');
    }

    $data = json_decode($response, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response: ' . json_last_error_msg());
    }

    if ($data === null) {
        $data = [];
    }

    if (isset($data['error'])) {
        throw new Exception($data['error']);
    }

    return $data;
}

function validateTransferCode(string $transferCode, int $totalCost): void
{
    centralbankPost('/transferCode', [
        'transferCode' => $transferCode,
        'totalCost'    => $totalCost
    ]);
}

function postReceipt(
    string $guestName,
    string $arrival,
    string $departure,
    array $features = [],
    int $stars = 5
): void {
    centralbankPost('/receipt', [
        'user'           => HOTEL_USER,
        'api_key'        => HOTEL_API_KEY,
        'guest_name'     => $guestName,
        'arrival_date'   => $arrival,
        'departure_date' => $departure,
        'features_used'  => $features,
        'star_rating'    => $stars
    ]);
}

function depositTransferCode(string $transferCode): void
{
    centralbankPost('/deposit', [
        'user'         => HOTEL_USER,
        'transferCode' => $transferCode
    ]);
}
