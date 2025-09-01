<?php
// pricing.php
session_start();
$active = 'pricing';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pricing | LearnLang Academy</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  </head>
  <body class="site">
    <a class="skip-link" href="#main">Skip to content</a>

    <?php include __DIR__ . '/partials/header.php'; ?>

    <!-- MAIN -->
    <main id="main" class="site-main">
      <!-- TITLE -->
      <section class="hero course-hero" aria-labelledby="pricing-title">
        <article class="course-header">
          <h1 id="pricing-title">Pricing Plans</h1>
          <p class="tagline">
            <em>Flexible and affordable plans to suit learners of all levels.</em>
          </p>
        </article>
      </section>

      <!-- PRICING GRID -->
      <section class="pricing-grid">
        <article class="pricing-card">
          <h3>Free</h3>
          <p class="price">$0 / month</p>
          <ul>
            <li>Basic language access</li>
            <li>Weekly practice sets</li>
            <li>Limited progress tracking</li>
          </ul>
        </article>

        <article class="pricing-card">
          <h3>Standard</h3>
          <p class="price">$9.99 / month</p>
          <ul>
            <li>Full language library</li>
            <li>Interactive lessons</li>
            <li>Progress dashboard</li>
            <li>Email support</li>
          </ul>
        </article>

        <article class="pricing-card">
          <h3>Pro</h3>
          <p class="price">$19.99 / month</p>
          <ul>
            <li>All Standard features</li>
            <li>1-on-1 coaching sessions</li>
            <li>Downloadable materials</li>
            <li>Completion certificates</li>
            <li>Priority support</li>
          </ul>
        </article>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="js/script.js"></script>
  </body>
</html>
