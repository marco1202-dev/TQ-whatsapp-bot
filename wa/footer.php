
<footer style="background-color: #000; color: #fff; padding: 30px 20px; font-family: Arial, sans-serif;">
  <div style="max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
      
      <!-- Logo -->
      <div style="flex: 1 1 200px; margin-bottom: 20px;">
        <img src="logo.png" alt="Logo" style="display: block; max-width: 150px; height: auto;">
      </div>
      
      <!-- Social Media Icons -->
      <div style="flex: 1 1 200px; text-align: right; margin-bottom: 20px;">
        <a href="#" target="_blank" rel="noopener" title="Facebook"
           style="display:inline-block; width:40px; height:40px; margin-left:10px; background:#3b5998; border-radius:50%; text-align:center; line-height:40px; color:#fff; font-size:18px;">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#" target="_blank" rel="noopener" title="Twitter"
           style="display:inline-block; width:40px; height:40px; margin-left:10px; background:#1da1f2; border-radius:50%; text-align:center; line-height:40px; color:#fff; font-size:18px;">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="#" target="_blank" rel="noopener" title="Instagram"
           style="display:inline-block; width:40px; height:40px; margin-left:10px; background:#e1306c; border-radius:50%; text-align:center; line-height:40px; color:#fff; font-size:18px;">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" target="_blank" rel="noopener" title="LinkedIn"
           style="display:inline-block; width:40px; height:40px; margin-left:10px; background:#0077b5; border-radius:50%; text-align:center; line-height:40px; color:#fff; font-size:18px;">
          <i class="fab fa-linkedin-in"></i>
        </a>
      </div>
    </div>

    <!-- Page Links -->
    <div style="display: flex; flex-wrap: wrap; gap: 20px; border-top: 1px solid #444; padding-top: 20px;">
      <a href="index.php" style="color: white; text-decoration: none;">Home</a>
      <a href="about.php" style="color: white; text-decoration: none;">About Us</a>
      <a href="services.php" style="color: white; text-decoration: none;">Services</a>
      
      <a href="contact.php" style="color: white; text-decoration: none;">Contact</a>
      <a href="privacy.php" style="color: white; text-decoration: none;">Privacy Policy</a>
      <a href="terms.php" style="color: white; text-decoration: none;">Terms of Service</a>
    </div>
  </div>
  	<p style="margin-left:16%;">&copy all rights reserved. waassist.io 2025</p>

</footer>

<!-- Font Awesome for Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


<!-- Include Font Awesome CDN for social icons -->






<style>
  .tab-button {
    background-color: rgb(209, 233, 255);
    border: none;
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .tab-button.active {
    background-color: #007BFF;
    color: #fff;
  }
  .tab-pane {
    display: none;
  }
  .tab-pane.active {
    display: block;
  }
</style>

<script>
  const tabContents = [
    {
      title: "Lead Qualification",
      text: "Optimize your lead funnel and qualify leads without human intervention using intelligent bots. Ask the right questions and segment based on quality.",
      image: "leadq.png"
    },
    {
      title: "Service Booking",
      text: "Allow customers to seamlessly book appointments or services via WhatsApp chatbots integrated with your calendar.",
      image: "service-booking.png"
    },
    {
      title: "Order Tracking",
      text: "Keep customers informed with real-time order status updates via automated WhatsApp messages.",
      image: "order-tracking.png"
    },
    {
      title: "Surveys & Feedback",
      text: "Gather insights and improve services by collecting customer opinions through chatbot surveys.",
      image: "survey.png"
    },
    {
      title: "Event Registration",
      text: "Simplify event sign-ups by enabling users to register directly through your WhatsApp bot.",
      image: "event.png"
    }
  ];

  function showTab(index) {
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach((btn, i) => btn.classList.toggle('active', i === index));

    const contentBox = document.getElementById('tab-contents');
    contentBox.innerHTML = `
      <div class="tab-pane active" style="flex: 1; min-width: 300px;">
        <h3>${tabContents[index].title}</h3>
        <p>${tabContents[index].text}</p>
      </div>
      <div style="flex: 1; min-width: 300px; text-align: center;">
        <img src="${tabContents[index].image}" alt="${tabContents[index].title}" style="width: 100%; max-width: 400px;">
      </div>
    `;
  }

  // Initialize with first tab
  showTab(0);
</script>



<!-- FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


<!-- Replace /path-to-your-*.jpg with actual image URLs -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
