<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>
<h2 class="mb-4">WhatsApp Bots</h2>
<a href="#" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Add Bot</a>
<table class="table table-bordered table-hover bg-white shadow-sm">
  <thead class="table-light">
    <tr>
      <th>#</th>
      <th>Bot Name</th>
      <th>Trigger</th>
      <th>Response</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>WelcomeBot</td>
      <td>"hi", "hello"</td>
      <td>"Hello! How can I help you?"</td>
      <td><span class="badge bg-success">Active</span></td>
      <td>
        <a href="#" class="btn btn-sm btn-info">View</a>
        <a href="#" class="btn btn-sm btn-warning">Edit</a>
        <a href="#" class="btn btn-sm btn-danger">Delete</a>
      </td>
    </tr>
    <!-- Dynamic bot entries go here -->
  </tbody>
</table>
<?php include 'includes/footer.php'; ?>
