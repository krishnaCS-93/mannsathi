<?php

/* =========================================
   DATABASE CONNECTION
========================================= */

$host = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("MySQL Connection Failed: " . $conn->connect_error);
}

$database = "mann_saathi";

$conn->query("
    CREATE DATABASE IF NOT EXISTS mann_saathi
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci
");

$conn->select_db($database);

$conn->set_charset("utf8mb4");

$conn->query("
    CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        rating INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

/* =========================================
   FEEDBACK SUBMISSION
========================================= */

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $rating = intval($_POST["rating"] ?? 0);
    $message = trim($_POST["message"] ?? "");

    if ($name === "" || $email === "" || $message === "") {

        $error = "Please fill all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($rating < 1 || $rating > 5) {

        $error = "Please select a rating.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO feedback (name, email, rating, message)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssis",
            $name,
            $email,
            $rating,
            $message
        );

        if ($stmt->execute()) {

            $success = true;

        } else {

            $error = "Something went wrong. Please try again.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Mann Saathi | Talk. Connect. Move Forward.</title>

<meta name="description"
      content="Mann Saathi - A supportive space to talk, connect and move forward.">


<style>

/* =========================================
   BASIC
========================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    scroll-behavior: smooth;
}

:root {

    --bg: #07111f;
    --bg2: #0c1d32;

    --card: rgba(255,255,255,.08);
    --card-border: rgba(255,255,255,.15);

    --text: #ffffff;
    --muted: #b8c7d9;

    --primary: #37d5a5;
    --secondary: #4d8dff;

    --shadow: 0 25px 60px rgba(0,0,0,.35);
}

body.light {

    --bg: #eef5ff;
    --bg2: #dcecff;

    --card: rgba(255,255,255,.65);
    --card-border: rgba(0,0,0,.08);

    --text: #101c2c;
    --muted: #526174;

    --shadow: 0 20px 45px rgba(40,80,120,.18);
}

body {

    font-family: Arial, Helvetica, sans-serif;

    background:
        radial-gradient(
            circle at 10% 10%,
            rgb(27,37,128),
            transparent 25%
        ),

        radial-gradient(
            circle at 90% 30%,
            rgba(55,213,165,.13),
            transparent 25%
        ),

        linear-gradient(
            135deg,
            var(--bg),
            var(--bg2)
        );

    color: var(--text);

    min-height: 100vh;

    overflow-x: hidden;

    transition: .4s;
}

a {
    text-decoration: none;
    color: inherit;
}

button {
    font-family: inherit;
}


/* =========================================
   PARTICLES
========================================= */

.particles {

    position: fixed;

    inset: 0;

    pointer-events: none;

    z-index: -1;

    overflow: hidden;
}

.particle {

    position: absolute;

    width: 5px;
    height: 5px;

    border-radius: 50%;

    background: var(--primary);

    opacity: .35;

    animation: float 12s linear infinite;
}

@keyframes float {

    0% {

        transform:
            translateY(100vh)
            translateX(0);

        opacity: 0;
    }

    15% {
        opacity: .5;
    }

    85% {
        opacity: .5;
    }

    100% {

        transform:
            translateY(-10vh)
            translateX(100px);

        opacity: 0;
    }
}


/* =========================================
   NAVBAR
========================================= */

nav {

    position: fixed;

    top: 15px;

    left: 50%;

    transform: translateX(-50%);

    width: min(1150px,92%);

    padding: 13px 18px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    background: rgba(8,20,35,.65);

    border: 1px solid var(--card-border);

    backdrop-filter: blur(18px);

    border-radius: 22px;

    z-index: 1000;

    box-shadow:
        0 15px 45px rgba(0,0,0,.18);
}

.logo {

    display: flex;

    align-items: center;

    gap: 10px;

    font-size: 21px;

    font-weight: bold;
}

.logo-icon {

    width: 42px;

    height: 42px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 13px;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            var(--secondary)
        );

    box-shadow:
        0 8px 25px
        rgba(55,213,165,.25);
}

.nav-links {

    display: flex;

    gap: 25px;
}

.nav-links a {

    font-size: 14px;

    color: var(--muted);

    transition: .3s;
}

.nav-links a:hover {
    color: var(--primary);
}

.nav-buttons {

    display: flex;

    gap: 8px;
}

.icon-btn {

    width: 40px;

    height: 40px;

    border:
        1px solid
        var(--card-border);

    background: var(--card);

    color: var(--text);

    border-radius: 12px;

    cursor: pointer;
}


/* =========================================
   HERO
========================================= */

.hero {

    width: min(1150px,92%);

    min-height: 100vh;

    margin: auto;

    display: grid;

    grid-template-columns: 1.1fr .9fr;

    align-items: center;

    gap: 50px;

    padding-top: 100px;
}

.badge {

    display: inline-block;

    padding: 9px 15px;

    border-radius: 30px;

    background:
        rgba(55,213,165,.1);

    border:
        1px solid
        rgba(55,213,165,.25);

    color: var(--primary);

    font-size: 13px;

    margin-bottom: 20px;
}

.hero h1 {

    font-size:
        clamp(45px,7vw,80px);

    line-height: .98;

    letter-spacing: -4px;

    margin-bottom: 22px;
}

.gradient {

    background:
        linear-gradient(
            90deg,
            var(--primary),
            #7bb2ff
        );

    -webkit-background-clip: text;

    color: transparent;
}

.hero p {

    max-width: 620px;

    color: var(--muted);

    font-size: 18px;

    line-height: 1.7;
}

.hero-buttons {

    display: flex;

    gap: 14px;

    margin-top: 30px;

    flex-wrap: wrap;
}

.btn {

    padding: 14px 22px;

    border-radius: 14px;

    border: none;

    cursor: pointer;

    font-weight: bold;

    transition: .3s;
}

.btn-primary {

    color: #06151a;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            #83ffe0
        );

    box-shadow:
        0 12px 35px
        rgba(55,213,165,.22);
}

.btn-secondary {

    color: var(--text);

    background: var(--card);

    border:
        1px solid
        var(--card-border);
}

.btn:hover {
    transform: translateY(-4px);
}


/* =========================================
   3D PROFILE
========================================= */

.scene {

    perspective: 1200px;

    display: flex;

    justify-content: center;
}

.profile-card {

    width: min(370px,90vw);

    height: 480px;

    position: relative;

    transform-style: preserve-3d;

    transform:
        rotateY(-12deg)
        rotateX(5deg);

    transition: .6s;
}

.profile-card:hover {

    transform:
        rotateY(0deg)
        rotateX(0deg)
        translateY(-10px);
}

.profile-card::before {

    content: "";

    position: absolute;

    inset: 20px;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            var(--secondary)
        );

    filter: blur(45px);

    opacity: .28;

    transform: translateZ(-60px);
}

.profile-inner {

    position: absolute;

    inset: 0;

    overflow: hidden;

    border-radius: 35px;

    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.17),
            rgba(255,255,255,.04)
        );

    border:
        1px solid
        rgba(255,255,255,.2);

    box-shadow: var(--shadow);

    backdrop-filter: blur(20px);

    transform-style: preserve-3d;
}

.profile-inner img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    object-position: center;

    filter:
        saturate(1.05)
        contrast(1.03);
}

.profile-overlay {

    position: absolute;

    inset: auto 0 0 0;

    padding: 30px;

    background:
        linear-gradient(
            transparent,
            rgba(0,0,0,.9)
        );

    transform: translateZ(35px);
}

.profile-overlay h3 {

    font-size: 26px;

    margin-bottom: 5px;
}

.profile-overlay p {
    color: #bceee1;
}

.floating-card {

    position: absolute;

    padding: 14px 17px;

    border-radius: 17px;

    background:
        rgba(10,25,42,.75);

    border:
        1px solid
        rgba(255,255,255,.15);

    backdrop-filter: blur(15px);

    box-shadow: var(--shadow);

    transform: translateZ(70px);
}

.float-one {

    top: 35px;

    left: -30px;
}

.float-two {

    bottom: 80px;

    right: -30px;
}


/* =========================================
   SECTIONS
========================================= */

section {

    width: min(1150px,92%);

    margin: 0 auto;

    padding: 100px 0;
}

.section-title {

    text-align: center;

    margin-bottom: 55px;
}

.section-title span {

    color: var(--primary);

    font-size: 13px;

    letter-spacing: 2px;

    text-transform: uppercase;
}

.section-title h2 {

    font-size: 42px;

    margin: 10px 0;
}

.section-title p {
    color: var(--muted);
}


/* =========================================
   SERVICES
========================================= */

.services {

    display: grid;

    grid-template-columns:
        repeat(3,1fr);

    gap: 22px;
}

.card {

    padding: 30px;

    border-radius: 25px;

    background: var(--card);

    border:
        1px solid
        var(--card-border);

    backdrop-filter: blur(15px);

    box-shadow: var(--shadow);

    transition: .4s;
}

.card:hover {

    transform:
        translateY(-12px)
        rotateX(3deg)
        rotateY(-2deg);

    border-color:
        rgba(55,213,165,.4);
}

.service-icon {

    width: 58px;

    height: 58px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 18px;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            var(--secondary)
        );

    font-size: 26px;

    margin-bottom: 22px;
}

.card h3 {

    margin-bottom: 12px;

    font-size: 21px;
}

.card p {

    color: var(--muted);

    line-height: 1.7;
}


/* =========================================
   STATS
========================================= */

.stats {

    display: grid;

    grid-template-columns:
        repeat(3,1fr);

    gap: 20px;
}

.stat {

    text-align: center;

    padding: 35px;

    border-radius: 25px;

    background: var(--card);

    border:
        1px solid
        var(--card-border);
}

.stat h3 {

    font-size: 40px;

    color: var(--primary);
}

.stat p {

    color: var(--muted);

    margin-top: 8px;
}


/* =========================================
   ABOUT
========================================= */

.about {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 30px;

    align-items: center;
}

.about-box {

    padding: 40px;

    border-radius: 30px;

    background: var(--card);

    border:
        1px solid
        var(--card-border);

    box-shadow: var(--shadow);
}

.about-box h2 {

    font-size: 40px;

    margin-bottom: 20px;
}

.about-box p {

    color: var(--muted);

    line-height: 1.8;

    margin-bottom: 15px;
}

.points {

    display: grid;

    gap: 13px;

    margin-top: 25px;
}

.point {

    display: flex;

    gap: 10px;

    align-items: center;

    color: var(--muted);
}

.check {
    color: var(--primary);
}


/* =========================================
   FEEDBACK
========================================= */

.feedback-wrapper {

    max-width: 750px;

    margin: auto;

    padding: 35px;

    border-radius: 30px;

    background: var(--card);

    border:
        1px solid
        var(--card-border);

    box-shadow: var(--shadow);
}

.form-group {

    margin-bottom: 18px;
}

.form-group label {

    display: block;

    margin-bottom: 8px;

    color: var(--muted);

    font-size: 14px;
}

input,
textarea,
select {

    width: 100%;

    padding: 15px;

    border-radius: 13px;

    border:
        1px solid
        var(--card-border);

    background:
        rgba(0,0,0,.15);

    color: var(--text);

    outline: none;
}

textarea {

    min-height: 130px;

    resize: vertical;
}

.rating {

    display: flex;

    gap: 8px;

    margin-top: 8px;
}

.star {

    font-size: 27px;

    cursor: pointer;

    opacity: .4;

    transition: .2s;
}

.star.active {

    opacity: 1;

    transform: scale(1.15);
}


/* =========================================
   ALERT
========================================= */

.alert {

    padding: 14px 18px;

    border-radius: 12px;

    margin-bottom: 20px;

    text-align: center;

    font-weight: bold;
}

.success {

    background:
        rgba(55,213,165,.15);

    border:
        1px solid
        rgba(55,213,165,.4);

    color: var(--primary);
}

.error {

    background:
        rgba(255,80,80,.12);

    border:
        1px solid
        rgba(255,80,80,.3);

    color: #ff8585;
}


/* =========================================
   FOOTER
========================================= */

footer {

    text-align: center;

    padding: 40px 20px;

    border-top:
        1px solid
        var(--card-border);

    color: var(--muted);
}

footer strong {
    color: var(--primary);
}


/* =========================================
   WHATSAPP
========================================= */

.whatsapp {

    position: fixed;

    right: 22px;

    bottom: 22px;

    width: 58px;

    height: 58px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #25D366;

    color: white;

    font-size: 28px;

    box-shadow:
        0 12px 35px
        rgba(37,211,102,.35);

    z-index: 999;

    transition: .3s;
}

.whatsapp:hover {

    transform:
        scale(1.1)
        translateY(-4px);
}


/* =========================================
   TOAST
========================================= */

.toast {

    position: fixed;

    left: 50%;

    bottom: 30px;

    transform:
        translate(-50%,100px);

    background: #10263b;

    color: white;

    padding: 14px 22px;

    border-radius: 13px;

    z-index: 2000;

    transition: .4s;

    opacity: 0;
}

.toast.show {

    transform:
        translate(-50%,0);

    opacity: 1;
}


/* =========================================
   MOBILE
========================================= */

@media(max-width:850px) {

    .nav-links {
        display: none;
    }

    .hero {

        grid-template-columns: 1fr;

        text-align: center;

        padding-top: 130px;
    }

    .hero p {
        margin: auto;
    }

    .hero-buttons {
        justify-content: center;
    }

    .scene {
        margin-top: 30px;
    }

    .services {
        grid-template-columns: 1fr;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    .about {
        grid-template-columns: 1fr;
    }

    .section-title h2 {
        font-size: 32px;
    }

    .profile-card {
        height: 430px;
    }
}

@media(max-width:500px) {

    nav {

        top: 8px;

        width: 95%;
    }

    .logo {
        font-size: 17px;
    }

    .hero h1 {

        font-size: 48px;

        letter-spacing: -2px;
    }

    .hero p {
        font-size: 16px;
    }

    .floating-card {

        font-size: 12px;

        padding: 10px;
    }

    .float-one {
        left: -10px;
    }

    .float-two {
        right: -10px;
    }

    .feedback-wrapper {
        padding: 22px;
    }
}

</style>

</head>


<body>


<!-- =========================================
     PARTICLES
========================================= -->

<div class="particles" id="particles"></div>


<!-- =========================================
     NAVBAR
========================================= -->

<nav>

    <a href="#home" class="logo">

        <div class="logo-icon">
            🫶
        </div>

        <span>
            Mann Saathi
        </span>

    </a>


    <div class="nav-links">

        <a href="#home">
            Home
        </a>

        <a href="#services">
            Services
        </a>

        <a href="#about">
            About
        </a>

        <a href="#feedback">
            Feedback
        </a>

    </div>


    <div class="nav-buttons">

        <button
            class="icon-btn"
            id="themeBtn">

            ☀️

        </button>

    </div>

</nav>


<!-- =========================================
     HERO
========================================= -->

<main id="home">

<section class="hero">


<div>

    <span class="badge">
        ✦ A safe space to be heard
    </span>


    <h1>

        Your thoughts<br>

        <span class="gradient">
            deserve a voice.
        </span>

    </h1>


    <p>

        Welcome to <b>Mann Saathi</b> —
        a friendly space where you can talk,
        share your thoughts, express yourself
        and move forward with a lighter mind.

    </p>


    <div class="hero-buttons">

        <a
            href="#feedback"
            class="btn btn-primary">

            💬 Share Your Thoughts

        </a>


        <a
            href="#services"
            class="btn btn-secondary">

            Explore Services →

        </a>

    </div>

</div>


<!-- 3D PROFILE -->

<div class="scene">

<div class="profile-card">


<div class="profile-inner">

<img
src="https://i.ibb.co/h51RvRT/a-clean-high-resolution-stylized-realistic-3d-cg-1.webp"
alt="Mann Saathi">


<div class="profile-overlay">

<h3>
Mann Saathi
</h3>

<p>
Talk • Connect • Move Forward
</p>

</div>

</div>


<div class="floating-card float-one">

💚 You're not alone

</div>


<div class="floating-card float-two">

✨ Keep moving forward

</div>


</div>

</div>

</section>


<!-- =========================================
     SERVICES
========================================= -->

<section id="services">


<div class="section-title">

<span>
WHAT WE OFFER
</span>

<h2>
Support that starts with listening.
</h2>

<p>
Simple conversations can sometimes make
a big difference.
</p>

</div>


<div class="services">


<div class="card">

<div class="service-icon">
💬
</div>

<h3>
Talk & Share
</h3>

<p>

Share your thoughts, worries and experiences
in a comfortable and supportive environment.

</p>

</div>


<div class="card">

<div class="service-icon">
🫂
</div>

<h3>
Friendly Support
</h3>

<p>

Sometimes you don't need a solution.
You simply need someone who listens.

</p>

</div>


<div class="card">

<div class="service-icon">
🌱
</div>

<h3>
Move Forward
</h3>

<p>

Reflect on your thoughts, find clarity and
take your next step with confidence.

</p>

</div>


</div>

</section>


<!-- =========================================
     STATS
========================================= -->

<section>

<div class="stats">


<div class="stat">

<h3
class="counter"
data-target="100">
0
</h3>

<p>
Conversations
</p>

</div>


<div class="stat">

<h3
class="counter"
data-target="50">
0
</h3>

<p>
People Supported
</p>

</div>


<div class="stat">

<h3
class="counter"
data-target="24">
0
</h3>

<p>
Hours to Listen
</p>

</div>


</div>

</section>


<!-- =========================================
     ABOUT
========================================= -->

<section id="about">

<div class="about">


<div class="about-box">

<h2>

What is

<span class="gradient">
Mann Saathi?
</span>

</h2>


<p>

Mann Saathi is built around a simple idea:
everyone deserves a place where they can
express themselves without feeling judged.

</p>


<p>

Our goal is to encourage positive conversations,
emotional support and personal growth.

</p>


<div class="points">


<div class="point">

<span class="check">
✓
</span>

A friendly listening space

</div>


<div class="point">

<span class="check">
✓
</span>

Respectful conversations

</div>


<div class="point">

<span class="check">
✓
</span>

Privacy-conscious experience

</div>


<div class="point">

<span class="check">
✓
</span>

Focus on positive progress

</div>


</div>

</div>


<div class="about-box">


<div class="service-icon">
🧠
</div>


<h3>
Your mind matters.
</h3>


<p>

Life can become busy, confusing and overwhelming.
Talking about what you're experiencing can be
a meaningful first step.

</p>


<p>

Mann Saathi is designed as a supportive platform,
not a replacement for professional medical or
mental-health care.

</p>


</div>

</div>

</section>


<!-- =========================================
     FEEDBACK
========================================= -->

<section id="feedback">


<div class="section-title">

<span>
YOUR VOICE
</span>

<h2>
Tell us what you feel.
</h2>

<p>
Your feedback helps us improve Mann Saathi.
</p>

</div>


<div class="feedback-wrapper">


<?php if ($success): ?>

<div class="alert success">

    Feedback submitted successfully! ❤️

</div>

<?php endif; ?>


<?php if ($error): ?>

<div class="alert error">

    <?php echo htmlspecialchars($error); ?>

</div>

<?php endif; ?>


<form
method="POST"
action="#feedback"
id="feedbackForm">


<div class="form-group">

<label>
Your Name
</label>

<input
type="text"
id="name"
name="name"
placeholder="Enter your name"
required>

</div>


<div class="form-group">

<label>
Email
</label>

<input
type="email"
id="email"
name="email"
placeholder="Enter your email"
required>

</div>


<div class="form-group">

<label>
How was your experience?
</label>


<div class="rating">

<span
class="star"
data-star="1">
★
</span>

<span
class="star"
data-star="2">
★
</span>

<span
class="star"
data-star="3">
★
</span>

<span
class="star"
data-star="4">
★
</span>

<span
class="star"
data-star="5">
★
</span>

</div>


<input
type="hidden"
id="rating"
name="rating"
value="0">

</div>


<div class="form-group">

<label>
Your Message
</label>

<textarea
id="message"
name="message"
placeholder="Write your thoughts..."
required></textarea>

</div>


<button
type="submit"
class="btn btn-primary"
style="width:100%;">

Send Feedback 🚀

</button>


</form>

</div>

</section>

</main>


<!-- =========================================
     WHATSAPP
========================================= -->

<a
class="whatsapp"
href="https://wa.me/919999999999"
target="_blank"
title="Chat on WhatsApp">

☎

</a>


<!-- =========================================
     FOOTER
========================================= -->

<footer>

<p>

© 2026

<strong>
Mann Saathi
</strong>.

Made with ❤️ for meaningful conversations.

</p>


<p
style="margin-top:10px;font-size:13px;">

Talk. Connect. Move Forward.

</p>

</footer>


<!-- =========================================
     TOAST
========================================= -->

<div
class="toast"
id="toast">

Feedback submitted successfully! ❤️

</div>


<script>


/* =========================================
   PARTICLES
========================================= */

const particleBox =
document.getElementById("particles");

for (let i = 0; i < 35; i++) {

    const particle =
    document.createElement("div");

    particle.className =
    "particle";

    particle.style.left =
    Math.random() * 100 + "%";

    particle.style.animationDelay =
    Math.random() * 10 + "s";

    particle.style.animationDuration =
    (8 + Math.random() * 10) + "s";

    particleBox.appendChild(particle);
}


/* =========================================
   DARK / LIGHT MODE
========================================= */

const themeBtn =
document.getElementById("themeBtn");

themeBtn.addEventListener(
"click",
() => {

    document.body.classList.toggle(
        "light"
    );

    if (
        document.body.classList.contains(
            "light"
        )
    ) {

        themeBtn.textContent = "🌙";

        localStorage.setItem(
            "mannTheme",
            "light"
        );

    } else {

        themeBtn.textContent = "☀️";

        localStorage.setItem(
            "mannTheme",
            "dark"
        );
    }

});


if (
localStorage.getItem("mannTheme")
=== "light"
) {

    document.body.classList.add(
        "light"
    );

    themeBtn.textContent = "🌙";
}


/* =========================================
   3D MOUSE EFFECT
========================================= */

const profile =
document.querySelector(
    ".profile-card"
);

document.addEventListener(
"mousemove",
(e) => {

    if (window.innerWidth < 850)
        return;

    const x =
        (window.innerWidth / 2 -
        e.clientX) / 35;

    const y =
        (window.innerHeight / 2 -
        e.clientY) / 35;

    profile.style.transform =
        `rotateY(${x}deg)
         rotateX(${-y}deg)`;

});


/* =========================================
   STAR RATING
========================================= */

let selectedRating = 0;

const stars =
document.querySelectorAll(
    ".star"
);

const ratingInput =
document.getElementById(
    "rating"
);

stars.forEach(star => {

    star.addEventListener(
    "click",
    () => {

        selectedRating =
            Number(
                star.dataset.star
            );

        ratingInput.value =
            selectedRating;

        stars.forEach(s => {

            s.classList.toggle(
                "active",
                Number(
                    s.dataset.star
                ) <= selectedRating
            );

        });

    });

});


/* =========================================
   COUNTER ANIMATION
========================================= */

const counters =
document.querySelectorAll(
    ".counter"
);

let counterStarted = false;

function startCounters() {

    if (counterStarted)
        return;

    const section =
    document.querySelector(
        ".stats"
    );

    const rect =
    section.getBoundingClientRect();

    if (
        rect.top <
        window.innerHeight - 100
    ) {

        counterStarted = true;

        counters.forEach(
        counter => {

            const target =
                Number(
                    counter.dataset.target
                );

            let current = 0;

            const step =
                Math.max(
                    1,
                    Math.ceil(
                        target / 50
                    )
                );

            const timer =
            setInterval(() => {

                current += step;

                if (
                    current >= target
                ) {

                    current =
                        target;

                    clearInterval(
                        timer
                    );
                }

                counter.textContent =
                    current + "+";

            },30);

        });

    }

}


window.addEventListener(
    "scroll",
    startCounters
);

startCounters();


/* =========================================
   SUCCESS MESSAGE
========================================= */

<?php if ($success): ?>

window.addEventListener(
"load",
function() {

    const toast =
        document.getElementById(
            "toast"
        );

    toast.classList.add(
        "show"
    );

    setTimeout(
    () => {

        toast.classList.remove(
            "show"
        );

    },3000);

});

<?php endif; ?>


/* =========================================
   SMOOTH NAVIGATION
========================================= */

document.querySelectorAll(
    'a[href^="#"]'
).forEach(link => {

    link.addEventListener(
    "click",
    (e) => {

        const id =
            link.getAttribute(
                "href"
            );

        if (id === "#")
            return;

        const target =
            document.querySelector(
                id
            );

        if (target) {

            e.preventDefault();

            target.scrollIntoView({
                behavior: "smooth"
            });

        }

    });

});

</script>


</body>
</html>