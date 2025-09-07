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

    <main id="main" class="site-main">
      <!-- HERO -->
      <section class="pr-hero" aria-labelledby="pricing-title">
        <header>
          <h1 id="pricing-title">Simple, honest pricing</h1>
          <p class="pr-muted"><em>Start free. Upgrade when you’re ready. Cancel anytime.</em></p>
        </header>
      </section>

      <!-- BILLING TOGGLE (kept separate so radios can control the plans via CSS) -->
      <section class="pr-toggle" aria-label="Billing period">
        <form aria-labelledby="billing-legend">
          <fieldset>
            <legend id="billing-legend" class="pr-visually-hidden">Billing period</legend>

            <input type="radio" name="billing" id="bill-monthly" checked />
            <label for="bill-monthly">Monthly</label>

            <input type="radio" name="billing" id="bill-yearly" />
            <label for="bill-yearly">Yearly <small class="pr-save">Save 20%</small></label>

            <span class="pr-track" aria-hidden="true"></span>
          </fieldset>
        </form>
      </section>

      <!-- PLANS -->
      <section class="pr-plans" aria-label="Plans">
        <ul class="pr-grid">
          <li class="pr-plan">
            <header class="pr-head">
              <h2>Starter</h2>
              <p class="pr-tag">Try the basics</p>
              <p class="pr-price">
                <span class="pr-pm">$0</span>
                <span class="pr-py">$0</span>
                <span class="pr-cycle">/ mo</span>
              </p>
            </header>
            <ul class="pr-features">
              <li>1 language course</li>
              <li>Weekly practice sets</li>
              <li>Basic progress tracking</li>
              <li>Community forum</li>
            </ul>
            <p class="pr-cta"><a class="pr-btn pr-btn-ghost" href="contact.php">Get started</a></p>
          </li>

          <li class="pr-plan pr-featured" aria-label="Most popular">
            <span class="pr-badge" aria-hidden="true">Most popular</span>
            <header class="pr-head">
              <h2>Growth</h2>
              <p class="pr-tag">Build momentum</p>
              <p class="pr-price">
                <span class="pr-pm">$12.99</span>
                <span class="pr-py">$9.99</span>
                <span class="pr-cycle">/ mo</span>
              </p>
            </header>
            <ul class="pr-features">
              <li>All languages</li>
              <li>Interactive lessons &amp; quizzes</li>
              <li>Streaks &amp; goals</li>
              <li>Progress dashboard</li>
              <li>Email support</li>
            </ul>
            <p class="pr-cta"><a class="pr-btn pr-btn-primary" href="contact.php">Choose Growth</a></p>
            <p class="pr-note">Yearly billing total: $119.88 charged once per year.</p>
          </li>

          <li class="pr-plan">
            <header class="pr-head">
              <h2>Pro</h2>
              <p class="pr-tag">Go all-in</p>
              <p class="pr-price">
                <span class="pr-pm">$24.99</span>
                <span class="pr-py">$19.99</span>
                <span class="pr-cycle">/ mo</span>
              </p>
            </header>
            <ul class="pr-features">
              <li>1-on-1 coaching sessions</li>
              <li>Downloadable materials</li>
              <li>Completion certificates</li>
              <li>Priority support</li>
            </ul>
            <p class="pr-cta"><a class="pr-btn pr-btn-ghost" href="contact.php">Go Pro</a></p>
          </li>
        </ul>
      </section>

      <!-- FAQ -->
      <section class="pr-faq" aria-label="Pricing FAQ">
        <header>
          <h2>Frequently asked questions</h2>
        </header>
        <ul class="pr-qa">
          <li>
            <h3>Can I cancel anytime?</h3>
            <p>Yes. Cancel in account settings and keep access until the end of your current period.</p>
          </li>
          <li>
            <h3>Do you offer student discounts?</h3>
            <p>Yes. Verified students receive 25% off Growth and Pro plans.</p>
          </li>
          <li>
            <h3>Is my data private?</h3>
            <p>Absolutely. We follow industry-standard security practices and never sell your data.</p>
          </li>
        </ul>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
    <script src="js/script.js"></script>
  </body>
</html>
