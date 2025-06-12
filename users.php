<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>
<h2 class="mb-4">Manage Users</h2>
<a href="#" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Add User</a>
<table class="table table-bordered table-hover bg-white shadow-sm">
  <thead class="table-light">
    <tr>
      <th>#</th>
      <th>Email</th>
      <th>Role</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>admin@example.com</td>
      <td>Admin</td>
      <td><span class="badge bg-success">Active</span></td>
      <td>
        <a href="#" class="btn btn-sm btn-info">View</a>
        <a href="#" class="btn btn-sm btn-warning">Edit</a>
        <a href="#" class="btn btn-sm btn-danger">Delete</a>
      </td>
    </tr>
    <!-- Add dynamic rows from JSON or DB -->
  </tbody>
</table>
<?php include 'includes/footer.php'; ?>
