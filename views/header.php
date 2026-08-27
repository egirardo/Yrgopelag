<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" href="assets/images/favicon-black.png" media="(prefers-color-scheme: light)">
    <link rel="icon" href="assets/images/favicon-white.png" media="(prefers-color-scheme: dark)">
    <link rel="stylesheet" href="assets/styles/custom.css">
    <link rel="stylesheet" href="assets/styles/base.css">
    <link rel="stylesheet" href="assets/styles/layout.css">
    <link rel="stylesheet" href="assets/styles/components.css">
    <link rel="stylesheet" href="assets/styles/rooms.css">
    <link rel="stylesheet" href="assets/styles/booking.css">
    <link rel="stylesheet" href="assets/styles/reviews.css">
</head>

<body data-bs-theme="dark">
    <?php require __DIR__ . "/navigation.php"; ?>