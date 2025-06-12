<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>
<h2 class="mb-4">Settings</h2>
<form class="bg-white p-4 shadow-sm rounded row g-3">
  <div class="col-md-6">
    <label>Business Name</label>
    <input type="text" class="form-control" placeholder="Business Name">
  </div>
  <div class="col-md-6">
    <label>Support Email</label>
    <input type="email" class="form-control" placeholder="email@example.com">
  </div>
  <div class="col-md-6">
    <label>Contact Phone</label>
    <input type="tel" class="form-control" placeholder="+1234567890">
  </div>
  <div class="col-md-6">
    <label>Logo Upload</label>
    <input type="file" class="form-control">
  </div>
  <div class="col-md-6">
    <label>Theme Color</label>
    <input type="color" class="form-control form-control-color">
  </div>
  <div class="col-md-6">
    <label>Timezone</label>
    <select class="form-select">
      <option value="UTC">UTC</option>
      <option value="Asia/Kolkata">Asia/Kolkata</option>
      <option value="Europe/London">Europe/London</option>
    </select>
  </div>
  
    <div class="col-md-6">
    <label>Facebook Phone Id </label>
    <input type="tel" class="form-control" placeholder="+1234567890">
    </div>
  
  
     <div class="col-md-6">
    <label>Facebook App Key </label>
    <input type="tel" class="form-control" placeholder="xdfgkoiutre4569ddloiVHYdgfjIOBHL25HH">
    </div>
	
	
	 <div class="col-md-6">
    <label>Message Script (JSON File) </label>
    <input type="file" class="form-control" placeholder="">
    </div>
  
  <div class="col-md-6">
    <label>Notifications</label>
    <select class="form-select">
      <option>Enabled</option>
      <option>Disabled</option>
    </select>
  </div>
  <div class="col-md-6">
    <label>Auto Backup</label>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="backup" id="daily" checked>
      <label class="form-check-label" for="daily">Daily</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="backup" id="weekly">
      <label class="form-check-label" for="weekly">Weekly</label>
    </div>
  </div>
  <div class="col-12">
    <label>Custom Welcome Message</label>
    <textarea class="form-control" rows="3" placeholder="Type welcome message..."></textarea>
  </div>
  <div class="col-12">
    <button class="btn btn-success">Save Settings</button>
  </div>
</form>

<?php include 'includes/footer.php'; ?>
