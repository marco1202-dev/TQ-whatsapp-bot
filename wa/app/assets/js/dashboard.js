document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    document.querySelector('.sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    // Initialize charts
    const messageCtx = document.getElementById('messageChart').getContext('2d');
    const messageChart = new Chart(messageCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [
                {
                    label: 'Incoming Messages',
                    data: [120, 190, 170, 220, 180, 150, 210],
                    borderColor: '#4a89dc',
                    backgroundColor: 'rgba(74, 137, 220, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Outgoing Messages',
                    data: [80, 120, 140, 160, 150, 110, 180],
                    borderColor: '#3b7dd8',
                    backgroundColor: 'rgba(59, 125, 216, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(performanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Successful', 'Failed', 'Pending'],
            datasets: [{
                data: [84, 10, 6],
                backgroundColor: [
                    '#4a89dc',
                    '#da4453',
                    '#f6bb42'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Make flow nodes draggable
    const flowNodes = document.querySelectorAll('.flow-node');
    const flowBuilder = document.querySelector('.flow-builder');

    flowNodes.forEach(node => {
        node.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.className);
        });
    });

    flowBuilder.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '#f0f4f8';
    });

    flowBuilder.addEventListener('dragleave', function() {
        this.style.backgroundColor = '#fafafa';
    });

    flowBuilder.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '#fafafa';
        
        const nodeType = e.dataTransfer.getData('text/plain');
        const newNode = document.createElement('div');
        newNode.className = nodeType;
        
        if (nodeType.includes('flow-node-start')) {
            newNode.innerHTML = '<i class="fas fa-play"></i> Start Node';
        } else if (nodeType.includes('flow-node-message')) {
            newNode.innerHTML = '<i class="fas fa-comment"></i> Message Node';
        } else if (nodeType.includes('flow-node-condition')) {
            newNode.innerHTML = '<i class="fas fa-code-branch"></i> Condition Node';
        } else if (nodeType.includes('flow-node-end')) {
            newNode.innerHTML = '<i class="fas fa-stop"></i> End Node';
        }
        
        newNode.style.margin = '10px 0';
        newNode.draggable = true;
        
        newNode.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.className);
        });
        
        this.appendChild(newNode);
    });
});


// In dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...
    
    // Edit Bot Modal Handler
    document.querySelectorAll('.edit-bot').forEach(button => {
        button.addEventListener('click', function() {
            const botId = this.dataset.botId;
            document.getElementById('editBotId').value = botId;
            document.getElementById('editBotName').value = this.dataset.botName;
            document.getElementById('editPhoneNumber').value = this.dataset.phoneNumber;
            document.getElementById('editFbAccessToken').value = this.dataset.accessToken;
            document.getElementById('editFbVerifyToken').value = this.dataset.verifyToken;
            document.getElementById('editFbPhoneNumberId').value = this.dataset.phoneNumberId;
            document.getElementById('editDescription').value = this.dataset.description;
        });
    });
});


// Add to dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    // Show BOT management section
    document.querySelectorAll('.sidebar-menu a[data-target="bot-management"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Hide all main content sections
            document.querySelectorAll('.main-content > .row:not(.bot-management-section)').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show BOT management section
            document.querySelector('.bot-management-section').style.display = 'block';
            
            // Update active menu
            document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Close BOT management section
    document.querySelector('.close-bot-section').addEventListener('click', function() {
        // Show all main content sections
        document.querySelectorAll('.main-content > .row').forEach(section => {
            section.style.display = 'block';
        });
        
        // Hide BOT management section
        document.querySelector('.bot-management-section').style.display = 'none';
        
        // Reset active menu to Dashboard
        document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
        document.querySelector('.sidebar-menu a[href="#dashboard"]').classList.add('active');
    });
});



document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('.main-content');
    const botOverlay = document.querySelector('.bot-management-overlay');
    const closeBtn = document.querySelector('.overlay-close-btn');

    // Show BOT management
    document.querySelectorAll('[data-target="bot-management"]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            mainContent.classList.add('blurred');
            botOverlay.classList.add('active');
        });
    });

    // Close BOT management
    closeBtn.addEventListener('click', () => {
        mainContent.classList.remove('blurred');
        botOverlay.classList.remove('active');
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            mainContent.classList.remove('blurred');
            botOverlay.classList.remove('active');
        }
    });
});



document.addEventListener('DOMContentLoaded', function() {
    const selectBot = document.getElementById('selectBot');
    const refreshBtn = document.getElementById('refreshChats');
    const chatList = document.getElementById('chatList');
    const loading = document.getElementById('loading');

    async function fetchMessages() {
        if (!selectBot.value) {
            chatList.innerHTML = '<div class="alert alert-info">Please select a BOT first</div>';
            return;
        }
        
        loading.style.display = 'block';
        chatList.innerHTML = '';

        try {
            const response = await fetch('fetch_chats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    bot_id: selectBot.value
                })
            });

            const data = await response.json();

            if (data.success) {
                if (data.messages.length === 0) {
                    chatList.innerHTML = '<div class="alert alert-info">No recent messages found</div>';
                } else {
                    chatList.innerHTML = data.messages.map(msg => `
                        <div class="chat-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${msg.from}</h6>
                                    <p class="mb-0">${msg.type === 'text' ? msg.text : `[${msg.type} message]`}</p>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">${msg.timestamp}</small>
                                    <div class="mt-1">
                                        <span class="badge bg-secondary">${msg.type}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            } else {
                chatList.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${data.message}
                        ${data.debug ? `<div class="mt-2"><small>Debug: ${JSON.stringify(data.debug)}</small></div>` : ''}
                    </div>
                `;
            }
        } catch (error) {
            chatList.innerHTML = `<div class="alert alert-danger">Network error: ${error.message}</div>`;
        } finally {
            loading.style.display = 'none';
        }
    }

    selectBot.addEventListener('change', fetchMessages);
    refreshBtn.addEventListener('click', fetchMessages);
});




document.addEventListener('DOMContentLoaded', function() {
    const flowBotSelect = document.getElementById('flowBotSelect');
    const flowsList = document.getElementById('flowsList');
    const flowForm = document.getElementById('flowForm');
    const flowId = document.getElementById('flowId');
    const flowName = document.getElementById('flowName');
    const flowBotId = document.getElementById('flowBotId');
    const flowJson = document.getElementById('flowJson');
    const deleteFlowBtn = document.getElementById('deleteFlowBtn');
    const refreshBtn = document.getElementById('refreshFlows');

    let currentFlows = [];

    // Load flows
    async function loadFlows() {
        try {
            const response = await fetch('get_flows.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ bot_id: flowBotSelect.value })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                showError('Failed to load flows: ' + (data.error || 'Unknown error'));
                return;
            }

            currentFlows = data.flows;
            renderFlows();

        } catch (error) {
            showError('Network error: ' + error.message);
        }
    }

    // Render flows list
    function renderFlows() {
        flowsList.innerHTML = '';
        
        currentFlows.forEach(flow => {
            const item = document.createElement('div');
            item.className = `list-group-item list-group-item-action d-flex justify-content-between align-items-center ${flow.is_default ? 'list-group-item-warning' : ''}`;
            item.innerHTML = `
                <div class="w-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${flow.flow_name}</strong>
                            <span class="badge bg-info ms-2">${flow.business_name}</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary edit-flow" data-id="${flow.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-flow" data-id="${flow.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">${flow.bot_id} | Last updated: ${new Date(flow.updated_at).toLocaleString()}</small>
                </div>
            `;
            flowsList.appendChild(item);
        });
    }

    // Handle form submission
    flowForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            // Validate JSON
            JSON.parse(flowJson.value);
        } catch (error) {
            showError('Invalid JSON: ' + error.message);
            return;
        }

        try {
            const response = await fetch('save_flow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: flowId.value || null,
                    bot_id: flowBotId.value,
                    flow_name: flowName.value,
                    flow_json: flowJson.value
                })
            });

            const data = await response.json();
            
            if (!data.success) {
                showError('Save failed: ' + (data.error || 'Unknown error'));
                return;
            }

            showSuccess('Flow saved successfully');
            resetForm();
            loadFlows();

        } catch (error) {
            showError('Network error: ' + error.message);
        }
    });

    // Delete flow
    flowsList.addEventListener('click', async (e) => {
        if (e.target.closest('.delete-flow')) {
            const flowId = e.target.closest('button').dataset.id;
            if (!confirm('Are you sure you want to delete this flow?')) return;

            try {
                const response = await fetch('delete_flow.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: flowId })
                });

                const data = await response.json();
                
                if (!data.success) {
                    showError('Delete failed: ' + (data.error || 'Unknown error'));
                    return;
                }

                showSuccess('Flow deleted successfully');
                loadFlows();

            } catch (error) {
                showError('Network error: ' + error.message);
            }
        }
    });

    // Edit flow
    flowsList.addEventListener('click', async (e) => {
        if (e.target.closest('.edit-flow')) {
            const flowId = e.target.closest('button').dataset.id;
            const flow = currentFlows.find(f => f.id == flowId);

            if (!flow) {
                showError('Flow not found');
                return;
            }

            try {
                flowForm.style.display = 'block';
                flowId.value = flow.id;
                flowName.value = flow.flow_name;
                flowBotId.value = flow.bot_id;
                flowJson.value = JSON.stringify(JSON.parse(flow.flow_json), null, 4);
                deleteFlowBtn.style.display = 'inline-block';
            } catch (error) {
                showError('Error parsing flow JSON: ' + error.message);
            }
        }
    });

    // New flow button
    document.getElementById('newFlowBtn').addEventListener('click', () => {
        flowForm.style.display = 'block';
        resetForm();
    });

    // Format JSON
    document.getElementById('formatJson').addEventListener('click', () => {
        try {
            flowJson.value = JSON.stringify(JSON.parse(flowJson.value), null, 4);
            document.getElementById('jsonError').textContent = '';
        } catch (error) {
            document.getElementById('jsonError').textContent = 'Invalid JSON: ' + error.message;
        }
    });

    // Reset form
    function resetForm() {
        flowForm.reset();
        flowId.value = '';
        deleteFlowBtn.style.display = 'none';
        document.getElementById('jsonError').textContent = '';
    }

    // Show/hide messages
    function showError(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.prepend(alert);
    }

    function showSuccess(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.prepend(alert);
    }

    // Initialize
    flowBotSelect.addEventListener('change', loadFlows);
    refreshBtn.addEventListener('click', loadFlows);
    loadFlows();
});




//paypal 

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(t => new bootstrap.Tooltip(t));

    // Plan selection handler
    document.querySelectorAll('.choose-plan').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const planId = this.dataset.planId;
            const planPrice = parseFloat(this.dataset.planPrice);
            
            if (planPrice > 0) {
                // Handle paid plan
                const paymentModal = new bootstrap.Modal('#paymentModal');
                paymentModal.show();
                
                // Initialize PayPal
                if (!window.paypal) {
                    await loadPayPalSDK();
                }
                
                initPayPalButton(planPrice, planId);
            } else {
                // Handle free plan switch
                if (confirm('Are you sure you want to switch to the Free plan?')) {
                    const response = await fetch('change_subscription.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ plan_id: planId })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        window.location.reload();
                    } else {
                        alert(result.error || 'Failed to switch plan');
                    }
                }
            }
        });
    });

    async function loadPayPalSDK() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = `https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=USD`;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function initPayPalButton(amount, planId) {
        paypal.Buttons({
            createOrder: (data, actions) => actions.order.create({
                purchase_units: [{ amount: { value: amount.toFixed(2) } }]
            }),
            onApprove: async (data, actions) => {
                try {
                    const details = await actions.order.capture();
                    const response = await fetch('handle_payment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            plan_id: planId,
                            payment_details: details
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        window.location.reload();
                    } else {
                        alert(result.error || 'Payment processing failed');
                    }
                } catch (error) {
                    alert('Error processing payment: ' + error.message);
                }
            },
            onError: err => alert('Payment failed: ' + err.message)
        }).render('#paypal-button-container');
    }
});





// settings 


document.addEventListener('DOMContentLoaded', function() {
    // Password Change Form
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value.trim();
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();

        if (newPassword !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        try {
            const response = await fetch('update_password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ currentPassword, newPassword })
            });
            
            const result = await response.json();
            if (result.success) {
                alert('Password updated successfully!');
                this.reset();
            } else {
                alert(result.error || 'Password update failed');
            }
        } catch (error) {
            alert('Error updating password: ' + error.message);
        }
    });

    // Payment Method Type Toggle
    document.getElementById('methodType').addEventListener('change', function() {
        document.querySelectorAll('.paypal-fields, .credit-card-fields')
               .forEach(el => el.style.display = 'none');
        
        if (this.value === 'paypal') {
            document.querySelector('.paypal-fields').style.display = 'block';
        } else if (this.value === 'credit_card') {
            document.querySelector('.credit-card-fields').style.display = 'block';
        }
    });

    // Add Payment Method
    document.getElementById('paymentMethodForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const methodType = document.getElementById('methodType').value;
        const formData = {
            methodType,
            paypalEmail: document.getElementById('paypalEmail').value,
            cardNumber: document.getElementById('cardNumber').value,
            expiryDate: document.getElementById('expiryDate').value,
            cvv: document.getElementById('cvv').value
        };

        try {
            const response = await fetch('add_payment_method.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            if (result.success) {
                window.location.reload();
            } else {
                alert(result.error || 'Failed to add payment method');
            }
        } catch (error) {
            alert('Error saving payment method: ' + error.message);
        }
    });

    // Delete Payment Method
    document.querySelectorAll('.delete-method').forEach(button => {
        button.addEventListener('click', async function() {
            if (confirm('Are you sure you want to delete this payment method?')) {
                const methodId = this.dataset.methodId;
                
                try {
                    const response = await fetch('delete_payment_method.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ methodId })
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.closest('.card').remove();
                    } else {
                        alert(result.error || 'Failed to delete payment method');
                    }
                } catch (error) {
                    alert('Error deleting payment method: ' + error.message);
                }
            }
        });
    });
});