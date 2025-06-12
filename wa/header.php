<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WAASSIST</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body, html {
      margin: 0;
      padding: 0;
    }
    .header {
      background: url('bg-header.png') no-repeat center center/cover;
      color: white;
      position: relative;
    }
    navbar {
      padding: 0.5rem 1rem;
      position: absolute;
      width: 100%;
      top: 0;
      left: 0;
      z-index: 1000;
    }
    
    .navbar-collapse {
      justify-content: flex-end;
    }

    .nav-link {
      margin: 0 12px;
      padding: 8px 15px !important;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler {
      border-color: rgba(255,255,255,0.5);
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Header Content Adjustments */
    .header-content {
      padding-top: 120px; /* Space for navbar */
      padding-bottom: 60px;
    }

    @media (max-width: 992px) {
      .navbar {
        position: relative;
        background: rgba(0,0,0,0.8) !important;
      }
      .header-content {
        padding-top: 40px;
      }
      .nav-link {
        margin: 5px 0;
        text-align: center;
      }
    }

    .plan-btn {
      padding: 10px 25px !important;
      border-radius: 8px !important;
      transition: all 0.3s ease !important;
      font-weight: 500 !important;
    }

    .btn-login {
      background: rgba(255, 255, 255, 0.1) !important;
      border: 2px solid rgba(255, 255, 255, 0.3) !important;
    }

    .btn-login:hover {
      background: rgba(255, 255, 255, 0.2) !important;
      transform: translateY(-2px);
    }

    .btn-join {
      background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
      border: none !important;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-join:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
    }

    /* Section targets */
    #features {
      scroll-margin-top: 100px;
    }

    #pricing {
      scroll-margin-top: 80px;
    }
    .nav-logo img {
      height: auto;
      width: 180px;
    }
    .header-content {
      min-height: 500px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      padding: 60px 20px;
      position: relative;
    }
    .left-content {
      max-width: 600px;
      width: 100%;
    }
    .tech-icons img {
      height: 40px;
      margin-right: 10px;
    }
    .right-image-box {
      width: 583px;
      height: 300px;
      background-color: rgba(255, 255, 255, 0.1);
      border: 2px dashed #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      position: relative;
    }
    .nav-link, .btn {
      color: white !important;
    }
    .btn-outline-light {
      border-color: white;
    }

    /* Responsive Fixes */
    @media (max-width: 1200px) {
      #rbox {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        margin: 30px auto;
        width: 100% !important;
        max-width: 693px;
        height: auto !important;
        aspect-ratio: 1/1;
      }
    }

    @media (max-width: 768px) {
      .header-content {
        padding-top: 30px;
        text-align: center;
      }
      .tech-icons {
        justify-content: center;
      }
      .features-grid {
        grid-template-columns: 1fr !important;
      }
      .testimonial-content {
        flex-direction: column;
      }
      .nav-logo img {
        width: 140px;
      }
      .header-content {
        min-height: auto;
        padding: 40px 20px;
      }
      .left-content h1 {
        font-size: 1.8rem;
      }
    }

    @media (max-width: 576px) {
      .navbar-nav {
        text-align: center;
        background: rgba(0,0,0,0.8);
        padding: 10px;
      }
      .right-image-box {
        width: 100%;
        height: 250px;
      }
      .features-grid {
        grid-template-columns: 1fr !important;
      }
      .tab-button {
        width: 100%;
      }
    }

    .social-icons a {
      display: inline-block;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      text-align: center;
      line-height: 40px;
      margin: 0 5px;
      color: white !important;
    }
    .facebook { background: #3b5998; }
    .twitter { background: #1da1f2; }
    .instagram { background: #e1306c; }
    .linkedin { background: #0077b5; }
  </style>
</head>
<body>

  <!-- Full Header including navbar -->
  <div class="header">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand nav-logo" href="#">
          <img src="logo.png" alt="WAASSIST">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto align-items-lg-center"> <!-- Changed to ms-auto -->
            <li class="nav-item">
              <a class="nav-link" href="#features">Features</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#pricing">Pricing</a>
            </li>
            <li class="nav-item ms-2">
              <a class="btn btn-login" href="http://localhost/wa/bot/">Login</a>
            </li>
            <li class="nav-item ms-2">
              <a class="btn btn-join" href="http://localhost/wa/bot/">Join Now</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Header Content -->
    <div class="container header-content">
      <div class="left-content">
        <h1><strong>Advanced AI Chat Interface</strong><br>with Customized Bot Creation with <strong>WAASSIST</strong></h1>
        <p class="mt-3">Elevate your user experience with an intelligent AI-powered chat interface that enables seamless communication and advanced bot creation.</p>
        <div class="tech-icons mt-4">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg" alt="Next.js">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg" alt="TypeScript">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg" alt="React">
        </div>
      </div>
      <div id="rbox" class="right-image-box">
        <img id="rotatingImage" src="image1.png" alt="Rotating" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
    </div>
  </div>

<script>
  const images = [
    'image1.png',
    'image2.png',
    'image3.png'
    
  ];

  let current = 0;
  const imgElement = document.getElementById('rotatingImage');

  setInterval(() => {
    // Fade out
    imgElement.style.opacity = 0;

    setTimeout(() => {
      // Change image
      current = (current + 1) % images.length;
      imgElement.src = images[current];

      // Fade in
      imgElement.style.opacity = 1;
    }, 1000); // wait for fade-out to finish
  }, 4000); // change image every 4 seconds
</script>

    </div>
  </div>