<?php
// course_nihonggo.php — Nihonggo Course page
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'courses'; // highlight Courses in header
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nihonggo Course | LearnLang Academy</title>
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
              src="images/flag-jp.png"
              alt="Flag of Japan"
              class="title-flag"
            />
          </figure>
          <h1 id="course-title">Nihonggo Course</h1>
          <p class="tagline">
            <em>Unlock everyday Japanese—clear, respectful, and confident.</em>
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
          <li>Survival phrases for greetings, shops, and travel</li>
          <li>Pronunciation & listening practice (kana + common words)</li>
          <li>Core grammar patterns with plenty of examples</li>
          <li>Cultural etiquette to sound natural and polite</li>
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
                <small>Hiragana & Katakana basics, greetings, numbers</small>
              </figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <figcaption>
                <strong>A2 – Daily Conversation</strong><br />
                <small>Shopping, transport, time, common expressions</small>
              </figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <figcaption>
                <strong>B1 – Practical Grammar</strong><br />
                <small
                  >Particles, verb forms (ます／辞書形), polite vs casual</small
                >
              </figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <figcaption>
                <strong>B2 – Situational Fluency</strong><br />
                <small
                  >Work & study topics, longer dialogues, keigo basics</small
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
        <a href="contact.php" class="btn btn-primary">Enroll in Nihonggo</a>
      </section>
    </main>

    <!-- FOOTER -->
    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="js/script.js"></script>
  </body>
</html>
