<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode($data) {
    $data = strtr($data, '-_', '+/');
    $padding = strlen($data) % 4;
    if ($padding) {
        $data .= str_repeat('=', 4 - $padding);
    }
    return base64_decode($data);
}

function signJWT($header, $payload, $secret) {
    $data = $header . '.' . $payload;
    return base64UrlEncode(hash_hmac('sha256', $data, $secret, true));
}

function decodeJWT($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return ['error' => 'Invalid JWT format'];
    }

    $header = json_decode(base64UrlDecode($parts[0]), true);
    $payload = json_decode(base64UrlDecode($parts[1]), true);
    return ['header' => $header, 'payload' => $payload];
}

function encodeJWT($header, $payload, $secret) {
    $headerEncoded = base64UrlEncode($header);
    $payloadEncoded = base64UrlEncode($payload);
    $signature = signJWT($headerEncoded, $payloadEncoded, $secret);
    return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
}

if (isset($input['jwt'])) {
    echo json_encode(decodeJWT($input['jwt']));
} elseif (isset($input['payload']) && isset($input['header'])) {
    $header = json_encode(json_decode($input['header'], true));
    $payload = json_encode(json_decode($input['payload'], true));
    echo json_encode(['encodedJWT' => encodeJWT($header, $payload, $input['secret'])]);
} else {
    echo json_encode(['error' => 'Invalid input data']);
}
?>
