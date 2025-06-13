<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Flow - WhatsApp Business Automation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .flow-editor {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 1000;
        }

        .flow-editor.active {
            display: block;
        }

        .flow-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #075E54;
            color: white;
        }

        .flow-content {
            display: flex;
            height: calc(100vh - 64px);
        }

        .flow-sidebar {
            width: 250px;
            background: #128C7E;
            color: white;
            padding: 1rem;
            overflow-y: auto;
        }

        .flow-canvas {
            flex: 1;
            background: #ECE5DD;
            position: relative;
            overflow: auto;
        }

        .node-item {
            background: #25D366;
            color: white;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border-radius: 0.5rem;
            cursor: grab;
            text-align: center;
            user-select: none;
            transition: all 0.2s;
        }

        .node-item:hover {
            background: #128C7E;
            transform: translateY(-2px);
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

        .connection {
            position: absolute;
            height: 2px;
            background: #4a5568;
            transform-origin: 0 0;
            pointer-events: none;
            z-index: 1;
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

        .flow-actions {
            display: flex;
            gap: 1rem;
        }

        .flow-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .flow-btn-primary {
            background: #25D366;
            color: white;
        }

        .flow-btn-secondary {
            background: #128C7E;
            color: white;
        }

        .flow-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Flow Editor -->
    <div id="flowEditor" class="flow-editor">
        <div class="flow-header">
            <h2 class="text-xl font-bold">Flow Editor</h2>
            <div class="flow-actions">
                <button id="saveFlowBtn" class="flow-btn flow-btn-primary">Save Flow</button>
                <button id="closeFlowBtn" class="flow-btn flow-btn-secondary">Close</button>
            </div>
        </div>
        <div class="flow-content">
            <div class="flow-sidebar">
                <h3 class="text-lg font-semibold mb-4">Nodes</h3>
                <div class="node-item" draggable="true" data-type="text">Simple Text</div>
                <div class="node-item" draggable="true" data-type="media">Media Files</div>
                <div class="node-item" draggable="true" data-type="buttons">Interactive Buttons</div>
                <div class="node-item" draggable="true" data-type="delay">Time Delay</div>
                <div class="node-item" draggable="true" data-type="http">HTTP Request</div>
            </div>
            <div id="canvas" class="flow-canvas"></div>
        </div>
    </div>

    <!-- Chat Flow List -->
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Chat Flows</h1>
            <button id="newFlowBtn" class="flow-btn flow-btn-primary">New Flow</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Flow cards will be added here dynamically -->
        </div>
    </div>

    <script>
        // Flow Editor State
        let nodes = [];
        let connections = [];
        let draggedNode = null;
        let startNode = null;
        let endNode = null;
        let isConnecting = false;
        let tempConnection = null;

        // DOM Elements
        const flowEditor = document.getElementById('flowEditor');
        const canvas = document.getElementById('canvas');
        const newFlowBtn = document.getElementById('newFlowBtn');
        const saveFlowBtn = document.getElementById('saveFlowBtn');
        const closeFlowBtn = document.getElementById('closeFlowBtn');

        // Event Listeners
        newFlowBtn.addEventListener('click', () => {
            flowEditor.classList.add('active');
            initializeFlow();
        });

        closeFlowBtn.addEventListener('click', () => {
            flowEditor.classList.remove('active');
            cleanupFlow();
        });

        saveFlowBtn.addEventListener('click', saveFlow);

        // Initialize Flow
        function initializeFlow() {
            nodes = [];
            connections = [];
            canvas.innerHTML = '';
        }

        // Cleanup Flow
        function cleanupFlow() {
            nodes = [];
            connections = [];
            canvas.innerHTML = '';
        }

        // Handle drag start for node items
        document.querySelectorAll('.node-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                draggedNode = {
                    type: e.target.dataset.type,
                    x: e.clientX,
                    y: e.clientY
                };
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
            }
            
            function stopDrag() {
                document.removeEventListener('mousemove', drag);
                document.removeEventListener('mouseup', stopDrag);
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

        // Delete node function
        function deleteNode(nodeId) {
            // Don't allow deleting the first node
            if (nodes.length === 1) {
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
            }
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
                alert('Flow saved successfully!');
                flowEditor.classList.remove('active');
                cleanupFlow();
            })
            .catch(error => {
                alert('Error saving flow: ' + error.message);
            });
        }
    </script>
</body>
</html> 