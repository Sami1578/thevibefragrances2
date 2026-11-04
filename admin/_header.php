<?php
require_once __DIR__ . '/_auth.php';
$cur = basename($_SERVER['SCRIPT_NAME']);
$pending = (int)(db_row("SELECT COUNT(*) AS n FROM orders WHERE status = 'pending'")['n'] ?? 0);
$nav = [
    'index.php' => ['Dashboard', '▦'],
    'products.php' => ['Products', '✦'],
    'banners.php' => ['Banners', '▭'],
    'categories.php' => ['Categories', '☰'],
    'orders.php' => ['Orders', '🛍'],
    'settings.php' => ['Settings', '⚙'],
];
$group = ['product-form.php' => 'products.php', 'banner-form.php' => 'banners.php', 'order-view.php' => 'orders.php'];
$active = $group[$cur] ?? $cur;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($adminTitle ?? 'Admin') ?> – <?= e(setting('site_name')) ?> Admin</title>
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>
<body>
<div class="shell">
  <aside class="side" id="side">
    <div class="brand">THE VIBE<small>ADMIN PANEL</small></div>
    <nav>
      <?php foreach ($nav as $file => [$label, $ico]): ?>
        <a href="<?= $file ?>" class="<?= $active === $file ? 'on' : '' ?>">
          <i><?= $ico ?></i><?= $label ?>
          <?php if ($file === 'orders.php' && $pending): ?><em><?= $pending ?></em><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="side-foot">
      <a href="<?= url('index.php') ?>" target="_blank">View website ↗</a>
      <a href="logout.php">Log out</a>
    </div>
  </aside>
  <div class="main">
    <header class="top">
      <button id="sideBtn" class="menu-btn" aria-label="Menu">☰</button>
      <h1><?= e($adminTitle ?? 'Admin') ?></h1>
      <span class="who">👤 <?= e($_SESSION['admin_name'] ?? 'admin') ?></span>
    </header>
    <div class="content">
<?php if ($f = flash()): ?>
      <div class="flash <?= e($f['t']) ?>"><?= e($f['m']) ?></div>
<?php endif; ?>
