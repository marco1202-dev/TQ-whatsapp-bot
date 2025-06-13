<?php
require_once '../app/includes/config.php';
require_once '../app/includes/functions.php';

// Check if user is logged in
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flow Management - WAassist</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Flow Management</h1>
            <a href="flow-builder.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                Create New Flow
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flow Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bot</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="flowsTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Flows will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function loadFlows() {
            fetch('get_flows.php')
                .then(response => response.json())
                .then(flows => {
                    const tableBody = document.getElementById('flowsTableBody');
                    tableBody.innerHTML = '';

                    if (flows.length === 0) {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No flows found. Create your first flow!
                            </td>
                        `;
                        tableBody.appendChild(row);
                        return;
                    }

                    flows.forEach(flow => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        
                        const createdAt = new Date(flow.created_at).toLocaleString();
                        const status = flow.is_default ? 'Default' : 'Custom';
                        const statusClass = flow.is_default ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${flow.flow_name}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${flow.bot_name}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                    ${status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${createdAt}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="flow-builder.php?id=${flow.id}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <button onclick="deleteFlow(${flow.id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to load flows', 'error');
                });
        }

        function deleteFlow(flowId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_flow.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: flowId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'Flow has been deleted.', 'success');
                            loadFlows(); // Reload the flows list
                        } else {
                            throw new Error(data.error || 'Failed to delete flow');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', error.message, 'error');
                    });
                }
            });
        }

        // Load flows when the page loads
        document.addEventListener('DOMContentLoaded', loadFlows);
    </script>
</body>
</html> 