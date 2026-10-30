<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | <?= htmlspecialchars($errorTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
    <link rel="stylesheet" href="layout/reports.css">
</head>
<body>
    <main class="page error-page">
        <div class="post-card error-card">
            <p class="error-code"><?= (int) $errorCode ?></p>
            <h1 class="page-title"><?= htmlspecialchars($errorTitle) ?></h1>
            <p><?= htmlspecialchars($errorMessage) ?></p>
            <a href="Newsfeed.php" class="btn btn-primary"><i class="bi bi-house-door"></i> Back to the newsfeed</a>
        </div>
    </main>
</body>
</html>