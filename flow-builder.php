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
            let nodes = [];
            let connections = [];
            let draggedNode = null;
            let startNode = null;
            let endNode = null;
            let isConnecting = false;
            let tempConnection = null;

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
            function createNode(type, x, y) {
                const node = document.createElement('div');
                node.className = 'node';
                node.style.left = x + 'px';
                node.style.top = y + 'px';
                
                const nodeId = 'node-' + Date.now();
                node.id = nodeId;
                
                let content = '';
                switch(type) {
                    case 'text':
                        content = `
                            <div class="node-header">
                                <span class="font-medium">Text Message</span>
                                <button class="delete-btn" onclick="deleteNode('${nodeId}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <textarea class="w-full p-2 border rounded" placeholder="Enter your message"></textarea>
                            </div>
                        `;
                        break;
                    case 'media':
                        content = `
                            <div class="node-header">
                                <span class="font-medium">Media Message</span>
                                <button class="delete-btn" onclick="deleteNode('${nodeId}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <input type="file" class="w-full p-2 border rounded">
                            </div>
                        `;
                        break;
                    case 'buttons':
                        content = `
                            <div class="node-header">
                                <span class="font-medium">Interactive Buttons</span>
                                <button class="delete-btn" onclick="deleteNode('${nodeId}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <div class="space-y-2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Button 1">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Button 2">
                                    <input type="text" class="w-full p-2 border rounded" placeholder="Button 3">
                                </div>
                            </div>
                        `;
                        break;
                    case 'delay':
                        content = `
                            <div class="node-header">
                                <span class="font-medium">Time Delay</span>
                                <button class="delete-btn" onclick="deleteNode('${nodeId}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <input type="number" class="w-full p-2 border rounded" placeholder="Delay in seconds">
                            </div>
                        `;
                        break;
                    case 'http':
                        content = `
                            <div class="node-header">
                                <span class="font-medium">HTTP Request</span>
                                <button class="delete-btn" onclick="deleteNode('${nodeId}')">Delete</button>
                            </div>
                            <div class="node-content">
                                <input type="text" class="w-full p-2 border rounded mb-2" placeholder="URL">
                                <select class="w-full p-2 border rounded">
                                    <option>GET</option>
                                    <option>POST</option>
                                    <option>PUT</option>
                                    <option>DELETE</option>
                                </select>
                            </div>
                        `;
                        break;
                }
                
                node.innerHTML = content;
                
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
                nodes.push({id: nodeId, element: node});
                log(`Node created: ${nodeId}`);

                // Disable delete button for first node
                if (nodes.length === 1) {
                    const deleteBtn = node.querySelector('.delete-btn');
                    if (deleteBtn) {
                        deleteBtn.disabled = true;
                    }
                }
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
                } else {
                    // End connection
                    if (node.id !== startNode.id) {  // Prevent self-connection
                        endNode = { id: node.id, element: node, handle: handle };
                        createConnection(startNode, endNode);
                    }
                    cleanupConnection();
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
                
                // Check if we're over a target handle
                const targetHandle = document.elementFromPoint(e.clientX, e.clientY);
                if (targetHandle && targetHandle.classList.contains('handle') && targetHandle.classList.contains('target')) {
                    const node = targetHandle.closest('.node');
                    if (node && node.id !== startNode.id) {  // Prevent self-connection
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
                
                // Add to connections array
                connections.push({
                    start: start,
                    end: end,
                    element: connection
                });
                
                // Update connection position
                updateConnections();
                log(`Created connection from ${start.id} to ${end.id}`);
            }

            function updateConnections() {
                connections.forEach(conn => {
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
                });
            }

            // Save flow
            function saveFlow() {
                const flowData = {
                    nodes: nodes.map(node => ({
                        id: node.id,
                        type: node.element.querySelector('.node-header span').textContent,
                        x: parseInt(node.element.style.left),
                        y: parseInt(node.element.style.top),
                        content: node.element.querySelector('.node-content').innerHTML
                    })),
                    connections: connections.map(conn => ({
                        from: conn.start.id,
                        to: conn.end.id
                    }))
                };
                
                log('Saving flow...');
                
                // Send to server
                fetch('save_flow.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(flowData)
                })
                .then(response => response.json())
                .then(data => {
                    log(`Flow saved successfully: ${JSON.stringify(data)}`);
                })
                .catch(error => {
                    log(`Error saving flow: ${error.message}`);
                });
            }

            // Add save button
            const saveButton = document.createElement('button');
            saveButton.className = 'fixed bottom-4 left-4 bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600';
            saveButton.textContent = 'Save Flow';
            saveButton.onclick = saveFlow;
            document.body.appendChild(saveButton);

            // Add delete node function
            function deleteNode(nodeId) {
                // Don't allow deleting the first node
                if (nodes.length === 1) {
                    log('Cannot delete the first node');
                    return;
                }

                // Remove connections
                connections = connections.filter(conn => {
                    if (conn.start.id === nodeId || conn.end.id === nodeId) {
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

            log('Flow builder initialized');
        });
    </script>
</body>
</html> 