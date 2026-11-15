<?php
/**
 * header.php
 * Common page header: <head>, navigation and the flash message.
 * Set $pageTitle before including this file.
 */
$pageTitle = $pageTitle ?? 'Contact Manager';
$flash     = pull_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - Contact Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php">Contact Manager</a>
        <nav class="main-nav">
            <a href="index.php">All contacts</a>
            <a href="add.php">Add contact</a>
        </nav>
    </div>
</header>

<main class="container">
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
