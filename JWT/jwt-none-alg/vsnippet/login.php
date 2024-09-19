<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="submit" value="Connexion">
</form>

<?php
// Active le tampon de sortie pour éviter toute sortie avant setcookie()
ob_start();

// Exemple d'utilisateur valide
$valid_user = "admin";
$valid_password = "password";

// Charger la clé privée pour signer le JWT
$privateKey = file_get_contents('private.pem');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $valid_user && $password === $valid_password) {
        // Génération du JWT avec l'algorithme RS256
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $payload = json_encode(['username' => $username, 'role' => 'admin', 'exp' => time() + 3600]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Signature du JWT avec la clé privée
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        // Stocker le JWT dans un cookie (expire dans une heure)
        setcookie('token', $jwt, time() + 3600, "/");

        // Terminer le tampon de sortie et envoyer le contenu
        ob_end_flush();

        // Afficher un message de confirmation
        echo "Connexion réussie. JWT stocké dans le cookie.";
    } else {
        echo "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>


