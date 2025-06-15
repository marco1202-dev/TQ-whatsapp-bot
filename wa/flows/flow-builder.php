<?php
require_once '../app/includes/config.php';
require_once '../app/includes/functions.php';
redirectIfNotLoggedIn();

$csrfToken = generateCsrfToken();

// Get user's bots
try {
    $stmt = $pdo->prepare("SELECT * FROM bots WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $bots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $bots = [];
    setFlash('error', 'Failed to load BOTs: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Flow Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f3f4f6;
        }
        
        .node-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: grab;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .node-item:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        
        .node-item:active {
            cursor: grabbing;
        }
        
        .flow-canvas {
            background-color: #f9fafb;
            background-image: 
                linear-gradient(rgba(0,0,0,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 100vh;
            position: relative;
        }
        
        .node {
            position: absolute;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            min-width: 200px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: move;
        }
        
        .node-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .node-content {
            font-size: 0.875rem;
            color: #4b5563;
        }
        
        .connection {
            position: absolute;
            height: 2px;
            background: #4a5568;
            transform-origin: 0 0;
            pointer-events: none;
            z-index: 1;
        }
        
        .handle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #4a5568;
            border-radius: 50%;
            cursor: crosshair;
            z-index: 2;
            transition: all 0.2s ease;
        }

        .handle.source {
            right: -4px;
            top: 50%;
            transform: translateY(-50%);
        }

        .handle.target {
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
        }

        .handle:hover {
            background: #2d3748;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.5);
            transform: translateY(-50%) scale(1.2);
        }

        .sidebar {
            background: white;
            border-right: 1px solid #e5e7eb;
            padding: 1.5rem;
            overflow-y: auto;
            height: 100vh;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .debug-info {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 0.5rem;
            border-radius: 0.25rem;
            font-family: monospace;
            font-size: 0.75rem;
            z-index: 1000;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .delete-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-1/4">
            <!-- Flow Details Section -->
            <div class="mb-6">
                <h2 class="section-title">Flow Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Flow Name</label>
                        <input type="text" id="flowName" class="w-full p-2 border rounded" placeholder="Enter flow name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Bot</label>
                        <select class="w-full p-2 border rounded" id="flowBotSelect">
                            <option value="">All Bots</option>
                            <?php foreach ($bots as $bot): ?>
                            <option value="<?= $bot['id'] ?>"><?= htmlspecialchars($bot['business_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <h2 class="section-title">Messages</h2>
            <div class="space-y-2">
                <div class="node-item" draggable="true" data-type="text">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="text-sm font-medium">Simple Text</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="media">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Media Files</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="buttons">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                            <span class="text-sm font-medium">Interactive Buttons</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="interactive-list">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                            <span class="text-sm font-medium">Interactive List</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="conversation-flow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                            <span class="text-sm font-medium">Conversation Flow</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="form">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                            <span class="text-sm font-medium">Form</span>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-title mt-8">Actions</h2>
            <div class="space-y-2">
                <div class="node-item" draggable="true" data-type="delay">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Time Delay</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="opt-out">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Opt-out</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="http">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-sm font-medium">HTTP Request</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="request-make">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-sm font-medium">Request Make</span>
                        </div>
                    </div>
                </div>
                <div class="node-item" draggable="true" data-type="request-zapier">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-sm font-medium">Request Zapier</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas -->
        <div class="flex-1 flow-canvas" id="canvas">
            <!-- Nodes will be added here dynamically -->
        </div>
    </div>

    <!-- Debug Info -->
    <div class="debug-info" id="debug"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('canvas');
            const debug = document.getElementById('debug');
            const flowNameInput = document.getElementById('flowName');
            const botSelect = document.getElementById('flowBotSelect');
            let nodes = [];
            let connections = [];
            let draggedNode = null;
            let startNode = null;
            let endNode = null;
            let isConnecting = false;
            let tempConnection = null;
            let currentFlowId = null;
            let isSaving = false; // Add flag to prevent duplicate saves

            // Function to get node content
            function getNodeContent(node) {
                const nodeElement = document.getElementById(node.id);
                if (!nodeElement) return null;

                const type = node.type;
                const content = {};

                switch(type) {
                    case 'text':
                        content.text = nodeElement.querySelector('textarea').value;
                        break;
                    case 'media':
                        content.file = nodeElement.querySelector('input[type="file"]').value;
                        break;
                    case 'buttons':
                        const inputs = nodeElement.querySelectorAll('input[type="text"]');
                        content.button1 = inputs[0].value;
                        content.button2 = inputs[1].value;
                        content.button3 = inputs[2].value;
                        break;
                    case 'delay':
                        content.delay = nodeElement.querySelector('input[type="number"]').value;
                        break;
                    case 'http':
                        content.url = nodeElement.querySelector('input[type="text"]').value;
                        content.method = nodeElement.querySelector('select').value;
                        break;
                }

                return content;
            }

            // Modify save function to include flow name and bot ID
            function saveFlow() {
                if (isSaving) return; // Prevent duplicate saves
                
                const flowName = flowNameInput.value.trim();
                const botId = botSelect.value;

                if (!flowName) {
                    Swal.fire('Error', 'Please enter a flow name', 'error');
                    return;
                }

                if (!botId) {
                    Swal.fire('Error', 'Please select a bot', 'error');
                    return;
                }

                isSaving = true; // Set saving flag

                // Collect node data with content
                const nodeData = nodes.map(node => {
                    const content = getNodeContent(node);
                    return {
                        id: node.id,
                        type: node.type,
                        x: parseInt(node.element.style.left),
                        y: parseInt(node.element.style.top),
                        content: content
                    };
                });

                const flowData = {
                    id: currentFlowId,
                    name: flowName,
                    bot_id: botId,
                    nodes: nodeData,
                    connections: connections.map(conn => ({
                        from: conn.start.id,
                        to: conn.end.id
                    }))
                };

                console.log('Saving flow data:', flowData); // Debug log

                fetch('save_flow.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(flowData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Flow saved successfully', 'success');
                        if (!currentFlowId) {
                            currentFlowId = data.flow_id;
                            // Update URL with the new flow ID
                            window.history.replaceState({}, '', `flow-builder.php?id=${data.flow_id}`);
                        }
                    } else {
                        throw new Error(data.error || 'Failed to save flow');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', error.message, 'error');
                })
                .finally(() => {
                    isSaving = false; // Reset saving flag
                });
            }

            // Add save button to the UI
            const saveButton = document.createElement('button');
            saveButton.className = 'fixed top-4 right-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors';
            saveButton.textContent = 'Save Flow';
            saveButton.addEventListener('click', saveFlow);
            document.body.appendChild(saveButton);

            // Load bots for selection
            function loadBots() {
                fetch('get_bots.php')
                    .then(response => response.json())
                    .then(data => {
                        botSelect.innerHTML = '<option value="">Select a bot</option>';
                        data.forEach(bot => {
                            const option = document.createElement('option');
                            option.value = bot.id;
                            option.textContent = bot.name;
                            botSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading bots:', error);
                        Swal.fire('Error', 'Failed to load bots', 'error');
                    });
            }

            // Load bots on page load
            loadBots();

            // Check if we're editing an existing flow
            const urlParams = new URLSearchParams(window.location.search);
            const flowId = urlParams.get('id');
            
            if (flowId) {
                loadExistingFlow(flowId);
            }

            // Function to load existing flow
            function loadExistingFlow(id) {
                fetch(`get_flow.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        
                        currentFlowId = id;
                        flowNameInput.value = data.name || '';
                        botSelect.value = data.bot_id || '';
                        
                        // Clear existing nodes and connections
                        canvas.innerHTML = '';
                        nodes = [];
                        connections = [];
                        
                        // Load nodes first
                        if (data.nodes && Array.isArray(data.nodes)) {
                        data.nodes.forEach(node => {
                            createNode(node.type, node.x, node.y, node.content, node.id);
                        });
                        }
                        
                        // Then create connections after all nodes are loaded
                        if (data.connections && Array.isArray(data.connections)) {
                        data.connections.forEach(conn => {
                                const startNode = nodes.find(n => n.id === conn.from);
                                const endNode = nodes.find(n => n.id === conn.to);
                                
                                if (startNode && endNode) {
                                    const start = {
                                        id: startNode.id,
                                        element: startNode.element,
                                        handle: startNode.element.sourceHandle
                                    };
                                    
                                    const end = {
                                        id: endNode.id,
                                        element: endNode.element,
                                        handle: endNode.element.targetHandle
                                    };
                                    
                                    createConnection(start, end);
                                }
                            });
                            // Force update after a short delay to ensure DOM is ready
                            setTimeout(() => {
                                updateConnections();
                                log('Forced updateConnections after loading all nodes and connections.');
                            }, 100);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading flow:', error);
                        Swal.fire('Error', 'Failed to load flow', 'error');
                    });
            }

            // Debug function
            function log(message) {
                debug.textContent = message;
                console.log(message);
            }

            // Handle drag start for node items
            document.querySelectorAll('.node-item').forEach(item => {
                item.addEventListener('dragstart', (e) => {
                    draggedNode = {
                        type: e.target.dataset.type,
                        x: e.clientX,
                        y: e.clientY
                    };
                    log(`Started dragging node of type: ${draggedNode.type}`);
                });
            });

            // Handle drop on canvas
            canvas.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            canvas.addEventListener('drop', (e) => {
                e.preventDefault();
                if (draggedNode) {
                    const rect = canvas.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    createNode(draggedNode.type, x, y);
                    log(`Created new node at position: ${x}, ${y}`);
                    draggedNode = null;
                }
            });

            // Create a new node
            function createNode(type, x, y, content = {}, id) {
                const node = document.createElement('div');
                node.className = 'node';
                node.style.left = x + 'px';
                node.style.top = y + 'px';
                
                node.id = id || 'node-' + Date.now();
                
                let nodeContent = '';
                switch(type) {
                    case 'text':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Text Message</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <textarea class="w-full p-2 border rounded" placeholder="Enter your message">${content.text || ''}</textarea>
                            </div>
                        `;
                        break;
                    case 'media':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Media Message</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <input type="file" class="w-full p-2 border rounded" value="${content.file || ''}">
                            </div>
                        `;
                        break;
                    case 'buttons':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Interactive Buttons</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Button 1" value="${content.button1 || ''}">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Button 2" value="${content.button2 || ''}">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Button 3" value="${content.button3 || ''}">
                                </div>
                            </div>
                        `;
                        break;
                    case 'delay':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Time Delay</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <input type="number" class="w-full p-2 border rounded" placeholder="Delay in seconds" value="${content.delay || '1'}">
                            </div>
                        `;
                        break;
                    case 'http':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">HTTP Request</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <input type="text" class="w-full p-2 border rounded mb-2" placeholder="URL" value="${content.url || ''}">
                                <select class="w-full p-2 border rounded">
                                    <option value="GET" ${(content.method === 'GET') ? 'selected' : ''}>GET</option>
                                    <option value="POST" ${(content.method === 'POST') ? 'selected' : ''}>POST</option>
                                    <option value="PUT" ${(content.method === 'PUT') ? 'selected' : ''}>PUT</option>
                                    <option value="DELETE" ${(content.method === 'DELETE') ? 'selected' : ''}>DELETE</option>
                                </select>
                            </div>
                        `;
                        break;
                    case 'interactive-list':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Interactive List</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="List Title">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="List Description">
                                    <div class="space-y-2">
                                        <input type="text" class="w-full p-2 border rounded" placeholder="List Item 1">
                                        <input type="text" class="w-full p-2 border rounded" placeholder="List Item 2">
                                        <input type="text" class="w-full p-2 border rounded" placeholder="List Item 3">
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'conversation-flow':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Conversation Flow</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Flow Name">
                                    <textarea class="w-full p-2 border rounded" placeholder="Flow Description" rows="3"></textarea>
                                    <select class="w-full p-2 border rounded">
                                        <option value="">Select Flow Type</option>
                                        <option value="linear">Linear Flow</option>
                                        <option value="branching">Branching Flow</option>
                                        <option value="conditional">Conditional Flow</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        break;
                    case 'form':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Form</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Form Title">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <input type="text" class="flex-1 p-2 border rounded" placeholder="Field Label">
                                            <select class="p-2 border rounded">
                                                <option value="text">Text</option>
                                                <option value="number">Number</option>
                                                <option value="email">Email</option>
                                                <option value="date">Date</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="text" class="flex-1 p-2 border rounded" placeholder="Field Label">
                                            <select class="p-2 border rounded">
                                                <option value="text">Text</option>
                                                <option value="number">Number</option>
                                                <option value="email">Email</option>
                                                <option value="date">Date</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'opt-out':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Opt-out</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Opt-out Message">
                                    <select class="w-full p-2 border rounded">
                                        <option value="permanent">Permanent Opt-out</option>
                                        <option value="temporary">Temporary Opt-out</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        break;
                    case 'request-make':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Request Make</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Make Scenario Name">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="API Key">
                                    <textarea class="w-full p-2 border rounded" placeholder="Request Parameters" rows="3"></textarea>
                                </div>
                            </div>
                        `;
                        break;
                    case 'request-zapier':
                        nodeContent = `
                            <div class="node-header">
                                <span class="font-medium">Request Zapier</span>
                                <button class="delete-btn" onclick="deleteNode('${node.id}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Zapier Webhook URL">
                                    <textarea class="w-full p-2 border rounded" placeholder="Request Payload" rows="3"></textarea>
                                </div>
                            </div>
                        `;
                        break;
                }
                
                node.innerHTML = nodeContent;
                
                // Add source handle
                const sourceHandle = document.createElement('div');
                sourceHandle.className = 'handle source';
                sourceHandle.addEventListener('mousedown', startConnection);
                node.appendChild(sourceHandle);
                
                // Add target handle
                const targetHandle = document.createElement('div');
                targetHandle.className = 'handle target';
                targetHandle.addEventListener('mousedown', startConnection);
                node.appendChild(targetHandle);
                
                // Make node draggable
                node.addEventListener('mousedown', startDragging);
                
                canvas.appendChild(node);
                nodes.push({
                    id: node.id,
                    element: node,
                    type: type,
                    x: x,
                    y: y,
                    content: content
                });
                log(`Node created: ${node.id}`);

                // Disable delete button for first node
                if (nodes.length === 1) {
                    const deleteBtn = node.querySelector('.delete-btn');
                    if (deleteBtn) {
                        deleteBtn.disabled = true;
                    }
                }

                // Store handle references
                node.sourceHandle = sourceHandle;
                node.targetHandle = targetHandle;
                
                return node;
            }

            // Handle node dragging
            function startDragging(e) {
                if (e.target.classList.contains('handle')) return;
                
                const node = e.target.closest('.node');
                const startX = e.clientX - node.offsetLeft;
                const startY = e.clientY - node.offsetTop;
                
                function drag(e) {
                    node.style.left = (e.clientX - startX) + 'px';
                    node.style.top = (e.clientY - startY) + 'px';
                    updateConnections();
                    log(`Dragging node: ${node.id}`);
                }
                
                function stopDrag() {
                    document.removeEventListener('mousemove', drag);
                    document.removeEventListener('mouseup', stopDrag);
                    log(`Stopped dragging node: ${node.id}`);
                }
                
                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', stopDrag);
            }

            // Handle connections
            function startConnection(e) {
                e.stopPropagation();
                const handle = e.target;
                const node = handle.closest('.node');
                if (!isConnecting) {
                    // Start new connection
                    isConnecting = true;
                    startNode = { id: node.id, element: node, handle: handle };
                    
                    // Create temporary connection line
                    tempConnection = document.createElement('div');
                    tempConnection.className = 'connection';
                    canvas.appendChild(tempConnection);
                    
                    log(`Started connection from node: ${node.id}`);
                    
                    // Add mouse move handler
                    document.addEventListener('mousemove', updateTempConnection);
                    document.addEventListener('mouseup', endConnection);
                }
            }

            function updateTempConnection(e) {
                if (!tempConnection || !startNode) return;
                
                const startRect = startNode.handle.getBoundingClientRect();
                const canvasRect = canvas.getBoundingClientRect();
                
                const startX = startRect.right - canvasRect.left;
                const startY = startRect.top + startRect.height / 2 - canvasRect.top;
                const endX = e.clientX - canvasRect.left;
                const endY = e.clientY - canvasRect.top;
                
                // Calculate length and angle
                const length = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
                const angle = Math.atan2(endY - startY, endX - startX) * 180 / Math.PI;
                
                // Update the connection element
                tempConnection.style.width = length + 'px';
                tempConnection.style.left = startX + 'px';
                tempConnection.style.top = startY + 'px';
                tempConnection.style.transform = `rotate(${angle}deg)`;
            }

            function endConnection(e) {
                if (!isConnecting) return;
                const targetHandle = document.elementFromPoint(e.clientX, e.clientY);
                if (targetHandle && targetHandle.classList.contains('handle') && targetHandle.classList.contains('target')) {
                    const node = targetHandle.closest('.node');
                    if (node && node.id !== startNode.id) {
                        // Use the actual handle reference
                        endNode = { id: node.id, element: node, handle: targetHandle };
                        createConnection(startNode, endNode);
                    }
                }
                cleanupConnection();
            }

            function cleanupConnection() {
                isConnecting = false;
                startNode = null;
                endNode = null;
                if (tempConnection) {
                    tempConnection.remove();
                    tempConnection = null;
                }
                document.removeEventListener('mousemove', updateTempConnection);
                document.removeEventListener('mouseup', endConnection);
            }

            function createConnection(start, end) {
                if (!start || !end || !start.id || !end.id) {
                    console.error('Invalid connection endpoints', start, end);
                    return;
                }
                // Check if connection already exists
                const existingConnection = connections.find(conn => 
                    conn.start.id === start.id && conn.end.id === end.id
                );
                if (existingConnection) {
                    log('Connection already exists');
                    return;
                }
                // Create connection element
                const connection = document.createElement('div');
                connection.className = 'connection';
                canvas.appendChild(connection);
                log(`Appended connection element to canvas: ${start.id} -> ${end.id}`);
                // Add to connections array
                const conn = {
                    start: start,
                    end: end,
                    element: connection
                };
                connections.push(conn);
                // Update connection position
                updateConnectionPosition(conn);
                log(`Created connection from ${start.id} to ${end.id}`);
            }

            // New function to update a single connection's position
            function updateConnectionPosition(conn) {
                    const startRect = conn.start.handle.getBoundingClientRect();
                    const endRect = conn.end.handle.getBoundingClientRect();
                    const canvasRect = canvas.getBoundingClientRect();
                    
                    const startX = startRect.right - canvasRect.left;
                    const startY = startRect.top + startRect.height / 2 - canvasRect.top;
                    const endX = endRect.left - canvasRect.left;
                    const endY = endRect.top + endRect.height / 2 - canvasRect.top;
                    
                    // Calculate length and angle
                    const length = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
                    const angle = Math.atan2(endY - startY, endX - startX) * 180 / Math.PI;
                    
                    // Update the connection element
                    conn.element.style.width = length + 'px';
                    conn.element.style.left = startX + 'px';
                    conn.element.style.top = startY + 'px';
                    conn.element.style.transform = `rotate(${angle}deg)`;
            }

            // Update the updateConnections function to use updateConnectionPosition
            function updateConnections() {
                connections.forEach(updateConnectionPosition);
            }

            // Add delete node function
            function deleteNode(nodeId) {
                // Don't allow deleting the first node
                if (nodes.length === 1) {
                    log('Cannot delete the first node');
                    return;
                }

                // Remove connections
                connections = connections.filter(conn => {
                    if (
                        (conn.start && conn.start.id === nodeId) ||
                        (conn.end && conn.end.id === nodeId)
                    ) {
                        conn.element.remove();
                        return false;
                    }
                    return true;
                });

                // Remove node
                const nodeIndex = nodes.findIndex(n => n.id === nodeId);
                if (nodeIndex !== -1) {
                    nodes[nodeIndex].element.remove();
                    nodes.splice(nodeIndex, 1);
                    log(`Node deleted: ${nodeId}`);
                }
            }

            window.deleteNode = deleteNode;

            // Add node to canvas
            function addNode(type) {
                const node = document.createElement('div');
                node.className = 'node absolute bg-white rounded-lg shadow-md p-4 cursor-move';
                node.style.left = '50px';
                node.style.top = '50px';
                node.id = `node-${Date.now()}`;
                node.dataset.type = type;

                let content = '';
                switch(type) {
                    case 'text':
                        content = `
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                            </div>
                        `;
                        break;
                    case 'media':
                        content = `
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700">Media File</label>
                                <input type="file" class="mt-1 block w-full">
                            </div>
                        `;
                        break;
                    case 'buttons':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Button 1</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Button 2</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Button 3</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        `;
                        break;
                    case 'delay':
                        content = `
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700">Delay (seconds)</label>
                                <input type="number" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        `;
                        break;
                    case 'http':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">URL</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Method</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        break;
                    case 'interactive-list':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">List Title</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">List Description</label>
                                    <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">List Item 1</label>
                                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">List Item 2</label>
                                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">List Item 3</label>
                                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'conversation-flow':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Flow Name</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Flow Description</label>
                                    <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Flow Type</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Flow Type</option>
                                        <option value="linear">Linear Flow</option>
                                        <option value="branching">Branching Flow</option>
                                        <option value="conditional">Conditional Flow</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        break;
                    case 'form':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Form Title</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <label class="block text-sm font-medium text-gray-700">Field Label</label>
                                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="block text-sm font-medium text-gray-700">Field Type</label>
                                        <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="text">Text</option>
                                            <option value="number">Number</option>
                                            <option value="email">Email</option>
                                            <option value="date">Date</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'opt-out':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opt-out Message</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opt-out Type</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="permanent">Permanent Opt-out</option>
                                        <option value="temporary">Temporary Opt-out</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        break;
                    case 'request-make':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Make Scenario Name</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">API Key</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Request Parameters</label>
                                    <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                                </div>
                            </div>
                        `;
                        break;
                    case 'request-zapier':
                        content = `
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Zapier Webhook URL</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Request Payload</label>
                                    <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                                </div>
                            </div>
                        `;
                        break;
                }

                node.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
                        <button onclick="deleteNode('${node.id}')" class="text-red-500 hover:text-red-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    ${content}
                    <div class="connection-points flex justify-between mt-2">
                        <div class="connection-point" data-point="start"></div>
                        <div class="connection-point" data-point="end"></div>
                    </div>
                `;

                canvas.appendChild(node);
                nodes.push({
                    id: node.id,
                    type: type,
                    element: node
                });

                // Make the node draggable
                makeNodeDraggable(node);
            }

            // Make node draggable
            function makeNodeDraggable(node) {
                let isDragging = false;
                let currentX;
                let currentY;
                let initialX;
                let initialY;
                let xOffset = 0;
                let yOffset = 0;

                node.addEventListener('mousedown', dragStart);
                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', dragEnd);

                function dragStart(e) {
                    // Only start drag if clicking on the node header or empty space
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                        return;
                    }

                    initialX = e.clientX - xOffset;
                    initialY = e.clientY - yOffset;

                    if (e.target === node || node.contains(e.target)) {
                        isDragging = true;
                    }
                }

                function drag(e) {
                    if (isDragging) {
                        e.preventDefault();
                        currentX = e.clientX - initialX;
                        currentY = e.clientY - initialY;

                        xOffset = currentX;
                        yOffset = currentY;

                        setTranslate(currentX, currentY, node);
                    }
                }

                function dragEnd(e) {
                    initialX = currentX;
                    initialY = currentY;
                    isDragging = false;
                }

                function setTranslate(xPos, yPos, el) {
                    el.style.transform = `translate3d(${xPos}px, ${yPos}px, 0)`;
                }
            }

            // Add a button to go to flows list
            const flowsListButton = document.createElement('a');
            flowsListButton.href = 'flows.php';
            flowsListButton.className = 'fixed top-4 right-32 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors';
            flowsListButton.textContent = 'Back to Flows List';
            document.body.appendChild(flowsListButton);

            log('Flow builder initialized');
        });
    </script>
</body>
</html> 