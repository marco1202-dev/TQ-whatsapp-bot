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
              <a class="btn btn-login" href="https://waassist.io/wa/bot/">Login</a>
            </li>
            <li class="nav-item ms-2">
              <a class="btn btn-join" href="https://waassist.io/wa/bot/">Join Now</a>
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
  
<section class="features-section" style="padding: 60px 20px; background-color: #fff;">
  <div class="container" style="max-width: 1200px; margin: 0 auto;">
    <h2 style="font-size: 36px; font-weight: bold; text-align: center; margin-bottom: 40px; line-height: 1.4;">
      Automate conversations with your customers,<br />
      from the first touch point to closing the sale
    </h2>

    <div class="content-wrapper" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; justify-content: center;">

      <!-- Video Thumbnail -->
      <div class="video-box" style="flex: 1 1 400px;
    max-width: 676px;
    height: 480px;">
        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/Eiz3MV4Up-A?si=JlEYOyntkbsMCIqh&amp" frameborder="0" allowfullscreen></iframe>
      </div>
	  
	  
      <!-- Features Grid -->
      <div class="features-grid" style="flex: 1 1 400px; max-width: 400px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

        <div class="feature-box" style="background: #f4f4f4; padding: 20px; border-radius: 8px; text-align: center;">
          <div style="font-size: 30px; margin-bottom: 10px;">📈</div>
          <div style="font-weight: bold; font-size: 16px;">Increase user engagement</div>
          <div style="font-size: 14px; color: #333; margin-top: 5px;">Subscribers are more likely to open triggered messages from your bot than bulk campaigns.</div>
        </div>

        <div class="feature-box" style="background: #f4f4f4; padding: 20px; border-radius: 8px; text-align: center;">
          <div style="font-size: 30px; margin-bottom: 10px;">💼</div>
          <div style="font-weight: bold; font-size: 16px;">Convert leads with ease</div>
          <div style="font-size: 14px; color: #333; margin-top: 5px;">Convert leads into customers by sending valuable content in your automated flows.</div>
        </div>

        <div class="feature-box" style="background: #f4f4f4; padding: 20px; border-radius: 8px; text-align: center;">
          <div style="font-size: 30px; margin-bottom: 10px;">📝</div>
          <div style="font-weight: bold; font-size: 16px;">Get customer feedback</div>
          <div style="font-size: 14px; color: #333; margin-top: 5px;">Build a better product or service by getting reviews from your customers.</div>
        </div>

        <div class="feature-box" style="background: #f4f4f4; padding: 20px; border-radius: 8px; text-align: center;">
          <div style="font-size: 30px; margin-bottom: 10px;">⏰</div>
          <div style="font-weight: bold; font-size: 16px;">Provide 24/7 support</div>
          <div style="font-size: 14px; color: #333; margin-top: 5px;">Create live chats to provide support and speedy answers 24/7.</div>
        </div>

      </div>
    </div>
  </div>
</section>



<section style="padding: 60px 20px; background-color: #ffffff;" id="features">
  <div style="max-width: 1200px; margin: auto; text-align: center;">
    <h2 style="font-size: 2.5rem; font-weight: 700; background: linear-gradient(to right, #000000, #007BFF); -webkit-background-clip: text; color: transparent;">Turn Your WhatsApp Conversations Into Revenue</h2>

    <!-- Tabs -->
    <div style="display: flex; justify-content: center; margin-top: 40px;">
      <div id="tab-buttons" style="display: flex; flex-wrap: wrap; gap: 10px;">
        <button class="tab-button active" onclick="showTab(0)">📈 Lead Qualification</button>
        <button class="tab-button" onclick="showTab(1)">💼 Service Booking</button>
        <button class="tab-button" onclick="showTab(2)">🛒 Order Tracking</button>
        <button class="tab-button" onclick="showTab(3)">📈 Surveys & Feedback</button>
        <button class="tab-button" onclick="showTab(4)">🎓 Event Registration</button>
      </div>
    </div>

    <!-- Tab Content -->
    <div id="tab-contents" style="margin-top: 0px; background-color: rgb(239, 246, 255); padding: 40px 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
      <div class="tab-pane" style="flex: 1; min-width: 300px;" id="tab-0">
        <h3>Lead Qualification</h3>
        <p>Optimize your lead funnel and qualify leads without human intervention using intelligent bots. Ask the right questions and segment based on quality.</p>
      </div>
      <div style="flex: 1; min-width: 300px; text-align: center;">
        <img src="leadq.png" alt="Lead Qualification" style="width: 100%; max-width: 400px;">
      </div>
    </div>
  </div>
</section>



<!-- Grow Your WhatsApp Audience Section -->
<section style="background: #ffffff; padding: 60px 20px; display: flex; align-items: center; justify-content: center;">
  <div style="display: flex; max-width: 1200px; width: 100%; gap: 40px;">
    
    <!-- Left Content -->
    <div style="flex: 1;">
      <h2 style="font-size: 32px; font-weight: bold; color: #000;">
        Grow Your WhatsApp Audience
      </h2>
      <p style="font-size: 16px; color: #333; margin-top: 20px;">
        Attract more chatbot subscribers by leveraging a website subscription widget, an automatically
        generated bio link page, or a QR code linked to your welcome flow. You can also send email or SMS
        invitations to your existing audiences, encouraging them to subscribe to your WhatsApp chatbot.
      </p>
    </div>

    <!-- Right Image -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
      <div style="width: 100%; max-width: 500px; height: 300px; background-color: #eee; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
        <span style="color: #999;"><img src="grow.png" style="width:463px;"></span>
      </div>
    </div>

  </div>
</section>


<section style="padding: 60px 20px; background-color: #f9f9f9;">
  <div style="max-width: 1300px; margin: auto; text-align: center;">
    <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 40px;">
      Flexible Pricing That Scales With Your Business
    </h2>

    <style>
      .pricing-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
      }

      .plan-card {
        background-color: rgb(239, 246, 255);
        flex: 1 1 280px;
        max-width: 300px;
        display: flex;
        flex-direction: column;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.08);
        min-height: 550px;
      }

      .plan-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
      }

      .plan-card p.price {
        font-size: 1.25rem;
        color: #007BFF;
        margin-bottom: 20px;
      }

      .plan-card ul {
        list-style: none;
        text-align: left;
        padding: 0;
        margin: 0 0 20px 0;
        flex-grow: 1;
      }

      .plan-card ul li {
        margin-bottom: 10px;
        padding-left: 20px;
        position: relative;
      }

      .plan-card ul li::before {
        content: "✔️";
        position: absolute;
        left: 0;
      }

      .plan-btn {
        background-color: #007BFF;
        color: white;
        padding: 12px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        margin-top: auto;
      }

      @media (max-width: 768px) {
        .plan-card {
          min-height: unset;
        }
      }
    </style>

    <div class="pricing-wrapper" id="pricing" >

      <!-- Free Plan -->
      <div class="plan-card">
        <div>
          <h3>Free</h3>
          <p class="price">$0 /month</p>
          <ul>
            <li>1 Team Member</li>
            <li>1,000 Subscribers</li>
            <li>5,000 AI Tokens</li>
            <li>Broadcast Campaigns</li>
            <li>Live Chat (Basic)</li>
          </ul>
        </div>
        <a href="https://waassist.io/wa/bot/" class="plan-btn">Start Free</a>
      </div>

      <!-- Basic Plan -->
      <div class="plan-card">
        <div>
          <h3>Basic</h3>
          <p class="price">$24 /month</p>
          <ul>
            <li>0% Markup Fees</li>
            <li>2 Team Members</li>
            <li>5,000 Subscribers</li>
            <li>100,000 AI Tokens</li>
            <li>Unlimited Message Credits</li>
            <li>Live Chat (Advanced)</li>
            <li>Campaigns & AI Replies</li>
          </ul>
        </div>
        <a href="https://waassist.io/wa/bot/" class="plan-btn">Get Started</a>
      </div>

      <!-- Pro Plan -->
      <div class="plan-card">
        <div>
          <h3>Pro</h3>
          <p class="price">$60 /month</p>
          <ul>
            <li>All Basic Features</li>
            <li>5 Team Members</li>
            <li>15,000 Subscribers</li>
            <li>Unlimited AI Tokens</li>
            <li>Webhook Workflows</li>
            <li>WhatsApp Catalog</li>
          </ul>
        </div>
        <a href="https://waassist.io/wa/bot/" class="plan-btn">Choose Pro</a>
      </div>

      <!-- Enterprise Plan -->
      <div class="plan-card">
        <div>
          <h3>Enterprise</h3>
          <p class="price">Contact Us</p>
          <ul>
            <li>All Pro Features</li>
            <li>Unlimited Team Members</li>
            <li>Unlimited Subscribers</li>
            <li>Dedicated Manager</li>
            <li>Custom Integration</li>
            <li>Priority Support</li>
          </ul>
        </div>
        <a href="https://waassist.io/wa/bot/" class="plan-btn">Talk to Sales</a>
      </div>

    </div>
  </div>
</section>




<section style="padding: 60px 20px; background-color: #f0f8ff;">
  <div style="max-width: 1000px; margin: auto; text-align: center;">
    <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 40px; color: #1e3a8a;">
      What Our Clients Say
    </h2>

    <style>
      .testimonial-carousel {
        position: relative;
        overflow: hidden;
        height: 300px;
      }

      .testimonial-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease-in-out;
        position: absolute;
        width: 100%;
        opacity: 0;
      }

      .testimonial-slide.active {
        opacity: 1;
        position: relative;
      }

      .testimonial-content {
        background-color: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 700px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 20px;
      }

      .testimonial-content img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
      }

      .testimonial-text {
        text-align: left;
      }

      .testimonial-text p {
        font-size: 1rem;
        color: #333;
        margin-bottom: 10px;
      }

      .testimonial-text h4 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: bold;
      }

      .testimonial-text span {
        font-size: 0.9rem;
        color: #666;
      }

      @media (max-width: 600px) {
        .testimonial-content {
          flex-direction: column;
          text-align: center;
        }

        .testimonial-text {
          text-align: center;
        }
      }
    </style>

    <div class="testimonial-carousel" id="testimonialCarousel">
      <div class="testimonial-slide active">
        <div class="testimonial-content">
          <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Client 1">
          <div class="testimonial-text">
            <p>"WAASSIST helped us double our engagement within a month. It's intuitive and powerful."</p>
            <h4>Sarah Johnson</h4>
            <span>Marketing Lead, FashionCo</span>
          </div>
        </div>
      </div>

      <div class="testimonial-slide">
        <div class="testimonial-content">
          <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Client 2">
          <div class="testimonial-text">
            <p>"Their automation flows made customer conversion effortless. Best platform we've used so far."</p>
            <h4>David Lee</h4>
            <span>Founder, TechNest</span>
          </div>
        </div>
      </div>

      <div class="testimonial-slide">
        <div class="testimonial-content">
          <img src="https://randomuser.me/api/portraits/women/3.jpg" alt="Client 3">
          <div class="testimonial-text">
            <p>"Customer support is now 24/7 and seamless. Our CSAT score went up by 40%!"</p>
            <h4>Amira Khalid</h4>
            <span>Customer Success, HealthPlus</span>
          </div>
        </div>
      </div>
    </div>

    <script>
      const slides = document.querySelectorAll(".testimonial-slide");
      let current = 0;

      setInterval(() => {
        slides[current].classList.remove("active");
        current = (current + 1) % slides.length;
        slides[current].classList.add("active");
      }, 5000);
    </script>
  </div>
</section>



<?php 

include("footer.php");

?>