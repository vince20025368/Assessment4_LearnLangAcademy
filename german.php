<?php
// course_german.php — German Course page
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'courses'; // highlight Courses in header
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>German Course | LearnLang Academy</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  </head>
  <body class="site">
    <a class="skip-link" href="#main">Skip to content</a>

    <!-- HEADER -->
    <?php include __DIR__ . '/partials/header.php'; ?>

    <!-- MAIN -->
    <main id="main" class="site-main">
      <!-- HERO -->
      <section class="hero course-hero" aria-labelledby="course-title">
        <article class="course-header">
          <figure>
            <img
              src="images/flag-de.png"
              alt="Flag of Germany"
              class="title-flag"
            />
          </figure>
          <h1 id="course-title">German Course</h1>
          <p class="tagline">
            <em
              >Think clearly and precisely—structured, strong, and surprisingly
              fun.</em
            >
          </p>
          <p class="cta-row">
            <a href="#modules" class="btn btn-ghost">View Modules</a>
            <a href="contact.php" class="btn btn-primary">Enroll Now</a>
          </p>
        </article>
      </section>

      <!-- WHAT YOU'LL GAIN -->
      <section class="course-section" aria-labelledby="details-title">
        <h2 id="details-title" class="course-h2">What you’ll gain</h2>
        <ul class="course-list">
          <li>Master greetings and everyday conversations in German</li>
          <li>Pronunciation and listening practice for clarity</li>
          <li>Grammar and vocabulary for study, work, and travel</li>
          <li>Cultural notes to communicate naturally and respectfully</li>
        </ul>
      </section>

      <!-- MODULES -->
      <section
        id="modules"
        class="languages course-section"
        aria-labelledby="modules-title"
      >
        <header>
          <h2 id="modules-title" class="course-h2">Modules & Levels</h2>
          <p class="section-sub">
            Each level includes speaking labs, quizzes, and feedback.
          </p>
        </header>

        <ul class="language-list">
          <li>
            <figure>
              <figcaption>
                <strong>Level A1 – Foundations</strong><br />
                <small
                  >Alphabet, sounds, greetings, numbers, simple questions</small
                >
              </figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <figcaption>
                <strong>A2 – Everyday German</strong><br />
                <small>Food, directions, schedules, daily expressions</small>
              </figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <figcaption>
                <strong>B1 – Practical Grammar</strong><br />
                <small
                  >Cases (nominative, accusative, dative), verbs, sentence
                  structure</small
                >
              </figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <figcaption>
                <strong>B2 – Fluency</strong><br />
                <small
                  >Workplace language, formal/informal forms, cultural
                  idioms</small
                >
              </figcaption>
            </figure>
          </li>
        </ul>
      </section>

      <!-- ENROLL -->
      <section class="course-section" aria-labelledby="enroll-title">
        <h2 id="enroll-title" class="course-h2">Ready to get started?</h2>
        <p class="course-note">
          Classes open monthly. We’ll match you to the right level.
        </p>
        <a href="contact.php" class="btn btn-primary">Enroll in German</a>
      </section>
    </main>

    <!-- FOOTER -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="js/script.js"></script>
  </body>
</html>
