<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; margin: 0; }
        .register-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .error { color: red; margin-bottom: 10px; font-size: 0.9em; }
        a { color: #007bff; text-decoration: none; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Registrace</h2>

    <?php if (!empty($message)): ?>
        <div class="error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="text" name="username" placeholder="Zvolte jméno" required>
        <input type="password" name="password" placeholder="Zvolte heslo" required>
        <button type="submit">Vytvořit účet</button>
    </form>
    
    <p><a href="index.php?page=login">Zpět na přihlášení</a></p>
</div>

</body>
</html>