<?php

define('PAYMONGO_SECRET_KEY', 'sk_test_BAp2tEAnHBUzzag8s5bHBBMS');

function getOrCreateEnrollmentPaymentLink($conn, $enrollmentId, $amountCentavos = 100, $description = 'Enrollment Formality Fee') {

    $stmt = $conn->prepare("SELECT checkout_url FROM enrollment_payments WHERE enrollment_id = ? LIMIT 1");
    $stmt->bind_param("i", $enrollmentId);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        return $existing['checkout_url'];
    }

    $payload = json_encode([
        'data' => [
            'attributes' => [
                'amount' => $amountCentavos,
                'description' => $description,
                'remarks' => "Enrollment ID: {$enrollmentId}",
            ],
        ],
    ]);

    $ch = curl_init('https://api.paymongo.com/v1/links');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':'),
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new Exception("PayMongo request failed: {$curlError}");
    }

    $decoded = json_decode($response, true);

    if ($httpCode !== 200 || empty($decoded['data']['attributes']['checkout_url'])) {
        $errorDetail = $decoded['errors'][0]['detail'] ?? $response;
        throw new Exception("PayMongo link creation failed (HTTP {$httpCode}): {$errorDetail}");
    }

    $linkId = $decoded['data']['id'];
    $checkoutUrl = $decoded['data']['attributes']['checkout_url'];

    $stmt = $conn->prepare("
        INSERT INTO enrollment_payments (enrollment_id, paymongo_link_id, checkout_url, amount)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("issi", $enrollmentId, $linkId, $checkoutUrl, $amountCentavos);
    $stmt->execute();
    $stmt->close();

    return $checkoutUrl;
}