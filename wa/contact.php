<?php include("header.php"); ?>

<main class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold">Get in Touch</h1>
      <p class="text-muted fs-5">We're here to help! Whether you have questions, feedback, or need support — we’d love to hear from you.</p>
    </div>

    <div class="row g-5">
      <!-- Contact Form -->
      <div class="col-lg-7">
        <div class="card shadow p-4 border-0 rounded-4">
          <h4 class="mb-4">Send Us a Message</h4>
          <form action="send_message.php" method="post">
            <div class="mb-3">
              <label for="name" class="form-label">Your Name *</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Your Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject">
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Message *</label>
              <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary px-4 py-2">Send Message</button>
          </form>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="col-lg-5">
        <div class="card shadow p-4 border-0 rounded-4 bg-white">
          <h4 class="mb-4">Contact Details</h4>
          <p><strong>waassist.io</strong><br>Your WhatsApp Automation Partner</p>
          <p><i class="bi bi-geo-alt-fill me-2 text-primary"></i>123 Innovation Street, Tech City, Dubai, UAE</p>
          <p><i class="bi bi-envelope-fill me-2 text-primary"></i><a href="mailto:support@waassist.io" class="text-decoration-none">support@waassist.io</a></p>
          <p><i class="bi bi-telephone-fill me-2 text-primary"></i><a href="tel:+971000000000" class="text-decoration-none">+971 00 000 0000</a></p>

          <hr class="my-4">

          <h6 class="mb-3">Business Hours</h6>
          <p>Monday - Friday: 9:00 AM – 6:00 PM<br>Saturday - Sunday: Closed</p>

          <hr class="my-4">

          <h6 class="mb-3">Follow Us</h6>
          <a href="#" class="text-primary me-3"><i class="bi bi-facebook fs-5"></i></a>
          <a href="#" class="text-primary me-3"><i class="bi bi-twitter-x fs-5"></i></a>
          <a href="#" class="text-primary me-3"><i class="bi bi-linkedin fs-5"></i></a>
        </div>
      </div>
    </div>

    <!-- Optional Map -->
    <div class="row mt-5">
      <div class="col">
        <div class="ratio ratio-16x9 rounded-4 shadow">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11521.3232!2d55.2708!3d25.2048!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43442ec7b47b%3A0x971e30e02151dc61!2sDubai!5e0!3m2!1sen!2sae!4v1680000000000"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include("footer.php"); ?>
