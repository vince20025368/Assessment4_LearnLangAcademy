<?php
// contact.php
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf   = $_SESSION['csrf'];
$active = 'contact'; // highlights "Contact Us" in the shared header

// If logged in, pull from session
$loggedIn     = !empty($_SESSION['account_id']);
$prefillName  = $loggedIn ? ($_SESSION['account_name'] ?? '') : '';
$prefillEmail = $loggedIn ? ($_SESSION['account_email'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Us | LearnLang Academy</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  </head>
  <body class="site">
    <a class="skip-link" href="#main">Skip to content</a>

    <?php include __DIR__ . '/partials/header.php'; ?>

    <main id="main" class="site-main">
      <section class="contact-header" aria-labelledby="contact-title">
        <h1 id="contact-title">Let’s Talk</h1>
        <p class="muted">We usually reply within one business day.</p>
      </section>

      <section class="contact-split" aria-label="Contact content">
        <aside aria-labelledby="details-title">
          <h2 id="details-title">Details</h2>
          <address>
            Level 1 &amp; 2, 17 O’Connell Street<br />
            Sydney NSW 2000, Australia
          </address>
          <p><strong>Hours</strong> Mon–Fri: 8:30am–5:00pm</p>
          <p><strong>Phone</strong> <a href="tel:+61292833583">(02) 9283 3583</a></p>
          <p><strong>Email</strong> <a href="mailto:ask@koi.edu.au">ask@koi.edu.au</a></p>
        </aside>

        <form action="backend/submit_contact.php" method="post" aria-labelledby="form-title">
          <fieldset>
            <legend id="form-title">Send a Message</legend>

            <!-- CSRF -->
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>" />

            <!-- Honeypot -->
            <input id="website" name="website" type="text" value="" tabindex="-1" autocomplete="off"
                   aria-hidden="true"
                   style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" />

            <!-- Full Name -->
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text"
                   value="<?= htmlspecialchars($prefillName, ENT_QUOTES) ?>"
                   <?= $loggedIn ? 'readonly' : '' ?>
                   required minlength="2" maxlength="100"
                   placeholder="Your name" />

            <!-- Email -->
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email"
                   value="<?= htmlspecialchars($prefillEmail, ENT_QUOTES) ?>"
                   <?= $loggedIn ? 'readonly' : '' ?>
                   required placeholder="you@example.com" />

            <!-- Subject -->
            <label for="subject">Subject</label>
            <input id="subject" name="subject" type="text"
                   required minlength="3" maxlength="150"
                   placeholder="Subject" />

            <!-- Message -->
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6"
                      required minlength="10" maxlength="1000"
                      placeholder="How can we help?"></textarea>

            <button type="submit" class="btn-accent">Send</button>
          </fieldset>
        </form>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
    <script src="js/script.js"></script>
  </body>
</html>
