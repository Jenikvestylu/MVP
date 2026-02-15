<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .login-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 10px; font-size: 0.9em; }
        .success { color: green; margin-bottom: 10px; font-size: 0.9em; }
        a { color: #007bff; text-decoration: none; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Přihlášení</h2>
    
    <?php if (!empty($message)): ?>
        <div class="error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
        <div class="success">Registrace úspěšná. Můžete se přihlásit.</div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="text" name="username" placeholder="Uživatelské jméno" required>
        <input type="password" name="password" placeholder="Heslo" required>
        <button type="submit">Přihlásit se</button>
    </form>
    
    <p>Nemáte účet? <a href="index.php?page=register">Zaregistrujte se zde</a></p>
</div>

</body>
</html>