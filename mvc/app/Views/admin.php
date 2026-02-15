<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Administrace</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: white; }
        .btn { padding: 5px 10px; cursor: pointer; border: none; color: white; border-radius: 3px; }
        .approve { background-color: green; }
        .reject { background-color: red; }
    </style>
</head>
<body>
    <h1>Administrace hlášení</h1>
    <p>Přihlášen: Admin | <a href="index.php">Zpět na mapu</a> | <a href="index.php?page=logout">Odhlásit</a></p>

    <?php if (count($pendingReports) == 0): ?>
        <p>Žádná nová hlášení ke kontrole.</p>
    <?php else: ?>
        <table>
            <tr><th>Kdo</th><th>Co</th><th>Kde</th><th>Akce</th></tr>
            <?php foreach ($pendingReports as $r): ?>
            <tr id="row-<?php echo $r['id']; ?>">
                <td><?php echo htmlspecialchars($r['username']); ?></td>
                <td><?php echo htmlspecialchars($r['description']); ?></td>
                <td><?php echo round($r['lat'],4) . ', ' . round($r['lon'],4); ?></td>
                <td>
                    <button class="btn approve" onclick="decide(<?php echo $r['id']; ?>, 'approve')">Schválit</button>
                    <button class="btn reject" onclick="decide(<?php echo $r['id']; ?>, 'delete')">Zamítnout</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <script>
    async function decide(id, action) {
        if(!confirm("Opravdu?")) return;
        // ZMĚNA: Volání přes Router
        const res = await fetch('index.php?page=api_reports', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, action: action })
        });
        if (res.ok) document.getElementById('row-' + id).remove();
    }
    </script>
</body>
</html>