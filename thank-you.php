<?php
// Reusable success page
$title = isset($_GET['title']) ? htmlspecialchars($_GET['title'], ENT_QUOTES) : 'Thank you! 🎉';
$msg   = isset($_GET['msg'])   ? htmlspecialchars($_GET['msg'], ENT_QUOTES)   : 'Your request has been received.';
$ref   = isset($_GET['ref'])   ? htmlspecialchars($_GET['ref'], ENT_QUOTES)   : '';
$back  = isset($_GET['back'])  ? htmlspecialchars($_GET['back'], ENT_QUOTES)  : 'index.html';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $title; ?> | LearnLang Academy</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  </head>
  <body class="site">
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
      <a href="index.html" class="logo-link" aria-label="LearnLang Academy Home">
        <img src="images/learnlang-logo.png" alt="LearnLang Academy logo" class="site-logo" />
        <span class="logo-text">LearnLang Academy</span>
      </a>
    </header>

    <main id="main" class="site-main">
      <section class="contact-header" aria-labelledby="thanks-title">
        <h1 id="thanks-title"><?php echo $title; ?></h1>
        <p class="muted"><?php echo $msg; ?></p>

        <?php if ($ref): ?>
          <p>Please keep your reference code: <strong><?php echo $ref; ?></strong></p>
        <?php endif; ?>

        <p><a href="<?php echo $back; ?>" class="btn-accent">Back</a></p>
      </section>
    </main>

    <footer class="site-footer">
      <p>&copy; 2025 LearnLang Academy. All rights reserved.</p>
    </footer>
  </body>
</html>
