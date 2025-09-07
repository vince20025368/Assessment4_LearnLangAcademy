<?php
// courses.php
session_start();
$active = 'courses';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Courses | LearnLang Academy</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  </head>
  <body class="site">
    <a class="skip-link" href="#main">Skip to content</a>

    <?php include __DIR__ . '/partials/header.php'; ?>

    <!-- MAIN -->
    <main id="main" class="site-main">
      <!-- INTRO -->
      <section class="hero" aria-labelledby="courses-title">
        <article>
          <h1 id="courses-title">Our Courses</h1>
          <p class="tagline">
            <em>Programs designed for real-world communication—beginner to advanced.</em>
          </p>
        </article>

        <figure>
          <img
            src="images/hero-courses.jpg"
            alt="Illustration of learners exploring different languages"
            class="hero-image"
          />
        </figure>
      </section>

      <!-- SHORT DESCRIPTION -->
      <section aria-labelledby="desc-title">
        <h2 id="desc-title" class="visually-hidden">About the Courses</h2>
        <p style="max-width: 72ch; margin: 0 auto; color: #5f6b7a">
          Choose a language below to view modules, schedules, and enrollment options.
          Each track includes speaking labs, listening drills, and cultural notes to
          help you apply what you learn immediately.
        </p>
      </section>

      <!-- COURSE GRID -->
      <section aria-labelledby="grid-title" class="languages">
        <header>
          <h2 id="grid-title">Browse Languages</h2>
          <p class="section-sub">Tap a course card to see syllabus and levels.</p>
        </header>

        <ul class="language-list">
          <li>
            <a href="tagalog.php" aria-label="View Tagalog course">
              <figure>
                <img src="images/flag-ph.png" alt="Flag of the Philippines" class="flag" />
                <figcaption>Tagalog</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="english.php" aria-label="View English course">
              <figure>
                <img src="images/flag-au.png" alt="Flag of Australia" class="flag" />
                <figcaption>English</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="spanish.php" aria-label="View Spanish course">
              <figure>
                <img src="images/flag-es.png" alt="Flag of Spain" class="flag" />
                <figcaption>Spanish</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="danish.php" aria-label="View Danish course">
              <figure>
                <img src="images/flag-dk.png" alt="Flag of Denmark" class="flag" />
                <figcaption>Danish</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="nihonggo.php" aria-label="View Japanese course">
              <figure>
                <img src="images/flag-jp.png" alt="Flag of Japan" class="flag" />
                <figcaption>Japanese (Nihongo)</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="chinese.php" aria-label="View Chinese course">
              <figure>
                <img src="images/flag-cn.png" alt="Flag of China" class="flag" />
                <figcaption>Chinese (Mandarin)</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="french.php" aria-label="View French course">
              <figure>
                <img src="images/flag-fr.png" alt="Flag of France" class="flag" />
                <figcaption>French</figcaption>
              </figure>
            </a>
          </li>

          <li>
            <a href="german.php" aria-label="View German course">
              <figure>
                <img src="images/flag-de.png" alt="Flag of Germany" class="flag" />
                <figcaption>German</figcaption>
              </figure>
            </a>
          </li>
        </ul>
      </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="js/script.js"></script>
  </body>
</html>
