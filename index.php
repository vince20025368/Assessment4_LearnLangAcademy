<?php
// index.php
session_start();
$active = 'home'; // highlights "Home" in the nav
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LearnLang Academy</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  </head>
  <body class="site">
    <a class="skip-link" href="#main">Skip to content</a>

    <!-- HEADER -->
    <?php include 'partials/header.php'; ?>

    <!-- MAIN -->
    <main id="main" class="site-main">
      <!-- HERO -->
      <section class="hero" aria-labelledby="hero-title">
        <article>
          <h1 id="hero-title">Breaking Barriers<br />Building Bridges</h1>
          <p class="tagline">
            <em>Learn at your own pace — anytime, anywhere.</em>
          </p>
          <p class="cta-row">
            <a href="courses.html" class="btn btn-primary">Explore Courses</a>
            <a href="features.html" class="btn btn-ghost">See Features</a>
          </p>
        </article>

        <figure>
          <img
            src="images/hero-image.jpg"
            alt="Silhouettes of people speaking different languages"
            class="hero-image"
          />
        </figure>
      </section>

      <!-- POPULAR LANGUAGES -->
      <section aria-labelledby="popular-languages" class="languages">
        <header>
          <h2 id="popular-languages">Popular Languages</h2>
          <p class="section-sub">Start with our most in-demand tracks.</p>
        </header>

        <ul class="language-list">
          <li>
            <figure>
              <img src="images/ph.png" alt="Flag of the Philippines" class="flag" />
              <figcaption>Tagalog</figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <img src="images/au.png" alt="Flag of Australia" class="flag" />
              <figcaption>English</figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <img src="images/fr.png" alt="Flag of France" class="flag" />
              <figcaption>French</figcaption>
            </figure>
          </li>
          <li>
            <figure>
              <img src="images/de.png" alt="Flag of Germany" class="flag" />
              <figcaption>German</figcaption>
            </figure>
          </li>
        </ul>
      </section>

      <!-- TESTIMONIALS -->
      <section class="testimonials" aria-labelledby="testimonials-title">
        <h2 id="testimonials-title">Client Testimonials</h2>

        <article class="testimonial-card">
          <figure>
            <img src="images/whang-od.png" alt="Whang-od" class="testimonial-photo" />
            <figcaption>Whang-od <span class="role">Student</span></figcaption>
          </figure>
          <p>“With LearnLang, I've developed my English speaking skills.”</p>
        </article>

        <article class="testimonial-card">
          <figure>
            <img src="images/jackie-chan.png" alt="Jackie Chan" class="testimonial-photo" />
            <figcaption>Jackie Chan <span class="role">Student</span></figcaption>
          </figure>
          <p>“I've gained confidence in speaking Tagalog for the first time.”</p>
        </article>
      </section>
    </main>

    <!-- FOOTER -->
    <?php include 'partials/footer.php'; ?>

    <script src="js/script.js"></script>
  </body>
</html>
