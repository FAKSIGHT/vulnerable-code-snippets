<?php
// Simplistic JWT verification
if (!isset($_COOKIE['token'])) {
    die('Pas de JWT trouvé dans le cookie.');
}

$jwt = $_COOKIE['token'];
$publicKey = file_get_contents('public.pem'); // Charger la clé publique pour vérifier le JWT

$parts = explode('.', $jwt);
if (count($parts) !== 3) {
    die('JWT invalide.');
}

$header = json_decode(base64_decode($parts[0]), true);
$payload = json_decode(base64_decode($parts[1]), true);
$signature_provided = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[2]));

// Vérifier si le JWT est expiré
if (isset($payload['exp']) && $payload['exp'] < time()) {
    die('Token expiré.');
}

// Vulnérabilité : si l'algorithme est "none", ne pas vérifier la signature
if ($header['alg'] === 'none') {
    echo "Bienvenue, " . htmlspecialchars($payload['username']) . "!";
    echo "<br>Vous avez accès à la page admin sans vérification de signature.";
} else {
    // Vérifier la signature avec la clé publique si l'algorithme est RS256
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    $verified = openssl_verify($base64UrlHeader . "." . $base64UrlPayload, $signature_provided, $publicKey, OPENSSL_ALGO_SHA256);

    if ($verified === 1) {
        echo "Bienvenue, " . htmlspecialchars($payload['username']) . "!";
        echo "<br>Vous avez accès à la page admin.";
    } elseif ($verified === 0) {
        echo "Signature invalide.";
    } else {
        echo "Erreur lors de la vérification du JWT.";
    }
}
?>