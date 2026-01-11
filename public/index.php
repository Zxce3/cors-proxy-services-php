<?php

// CORS Proxy Service

// Configuration: Allowed Origins
// Set to ['*'] to allow all, or specific domains like ['https://myapp.com', 'http://localhost:3000']
$allowedOrigins = ['*'];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Check if origin is allowed
if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
    // If specific origin is matched, echo it back, otherwise '*' (if configured)
    $allowOriginRaw = in_array('*', $allowedOrigins) ? '*' : $origin;
    header("Access-Control-Allow-Origin: $allowOriginRaw");
} else {
    // If origin is not allowed and we are not in public mode, simple exit or continue without headers
    // For a strict secure service, we might want to deny here. 
    // But often proxies just don't send the headers, letting browser block.
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the target URL from the query parameter
$url = isset($_GET['url']) ? $_GET['url'] : null;

if (!$url) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing "url" query parameter.']);
    exit;
}

// Basic validation (you might want to enhance this)
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL provided.']);
    exit;
}

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// Forward user agent to avoid being blocked by some sites
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Compatible; CORS-Proxy/1.0)');

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$curlError = curl_error($ch);

curl_close($ch);

if ($response === false) {
    http_response_code(502); // Bad Gateway
    echo json_encode(['error' => 'Failed to fetch resource.', 'details' => $curlError]);
    exit;
}

// Forward the content type from the destination
if ($contentType) {
    header("Content-Type: " . $contentType);
}

// Output the response
http_response_code($httpCode);
echo $response;
