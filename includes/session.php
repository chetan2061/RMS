<?php
// Configure secure session cookie parameters



session_set_cookie_params([
    // 'lifetime' => 0,
    // 'path'     => '/',
    // 'domain'   => '',
    // 'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);


// Start the session
session_start();
?>
