<?php
require_once __DIR__ . '/functions.php';
$siteName = setting('site_name', 'The Vibe Fragrances');
$cartCount = cart_count();
$pageTitle = isset($pageTitle) ? $pageTitle . ' | ' . $siteName : $siteName . ' – ' . setting('tagline');
$cats = db_all("SELECT * FROM categories ORDER BY sort_order, name");
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDesc ?? setting('tagline')) ?>">
<meta name="csrf" content="<?= csrf_token() ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<?php if (setting('announcement')): ?>
<div class="announce"><span><?= e(setting('announcement')) ?></span></div>
<?php endif; ?>

<header class="site-header" id="siteHeader">
  <div class="container nav">
    <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
    <a class="logo" href="<?= url('index.php') ?>">
      <span class="logo-main">THE VIBE</span>
      <span class="logo-sub">FRAGRANCES</span>
    </a>
    <nav class="menu" id="menu">
      <a href="<?= url('index.php') ?>" class="<?= $current === 'index.php' ? 'active' : '' ?>">Home</a>
      <a href="<?= url('shop.php') ?>" class="<?= $current === 'shop.php' ? 'active' : '' ?>">Shop All</a>
      <?php foreach ($cats as $c): ?>
        <a href="<?= url('shop.php?cat=' . $c['id']) ?>"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(wa_link('Hello! I would like to know more about your fragrances.')) ?>" target="_blank" rel="noopener">Contact</a>
    </nav>
    <div class="nav-actions">
      <form class="search" action="<?= url('shop.php') ?>" method="get">
        <input type="search" name="q" placeholder="Search perfumes…" value="<?= e($_GET['q'] ?? '') ?>">
        <button aria-label="Search">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        </button>
      </form>
      <a class="cart-btn" href="<?= url('cart.php') ?>" aria-label="Cart">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7V6a3 3 0 016 0v1"/></svg>
        <span class="cart-count" id="cartCount" <?= $cartCount ? '' : 'style="display:none"' ?>><?= $cartCount ?></span>
      </a>
    </div>
  </div>
</header>
<div class="toast" id="toast"></div>
<main>
