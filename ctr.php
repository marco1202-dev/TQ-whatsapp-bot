<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete WhatsApp Flow Builder</title>
<style>
  body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    display: flex;
    height: 100vh;
    overflow: hidden;
    color: #000;
  }
  
  #sidebar {
    width: 220px;
    background: #075E54;
    color: #fff;
    padding: 15px;
    box-sizing: border-box;
    overflow-y: auto;
  }
  
  #canvas {
    flex: 1;
    background: #ECE5DD;
    position: relative;
    overflow: auto;
  }
  
  .node-item {
    background: #128C7E;
    color: #fff;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    cursor: grab;
    text-align: center;
    user-select: none;
  }
  
  .node {
    position: absolute;
    background: #fff;
    border: 2px solid #075E54;
    border-radius: 8px;
    padding: 15px;
    width: 250px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    cursor: move;
    user-select: none;
    z-index: 1;
  }
  
  .node-header {
    font-weight: bold;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .node-content {
    font-size: 14px;
  }
  
  .output-connector {
    width: 14px;
    height: 14px;
    background: #34B7F1;
    border-radius: 50%;
    position: absolute;
    right: -7px;
    top: 50%;
    transform: translateY(-50%);
    cursor: crosshair;
    z-index: 10;
  }
  
  .button-connector {
    width: 12px;
    height: 12px;
    background: #FF9800;
    border-radius: 50%;
    position: absolute;
    right: -6px;
    cursor: crosshair;
    z-index: 10;
  }
  
  .connection {
    position: absolute;
    height: 2px;
    background: #34B7F1;
    transform-origin: 0 0;
    z-index: 0;
    pointer-events: none;
  }
  
  .button-connection {
    position: absolute;
    height: 2px;
    background: #FF9800;
    transform-origin: 0 0;
    z-index: 0;
    pointer-events: none;
  }
  
  #saveBtn {
    background: #25D366;
    width: 100%;
    padding: 10px;
    margin-top: 20px;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
  }
  
  textarea, input, select {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    background: #fff;
  }
  
  .btn {
    background: #25D366;
    color: #fff;
    border: none;
    padding: 6px 12px;
    margin: 3px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
  }
  
  .btn-red {
    background: #FF3B30;
  }
  
  .btn-blue {
    background: #34B7F1;
  }
  
  .btn-small {
    padding: 3px 8px;
    font-size: 12px;
  }
  
  .button-list {
    margin-top: 10px;
    position: relative;
  }
  
  .button-item {
    display: flex;
    margin-bottom: 5px;
    align-items: center;
    position: relative;
  }
  
  .button-item input {
    flex: 1;
    margin-right: 5px;
  }
  
  .remove-btn {
    background: #FF3B30;
    color: #fff;
    border: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  #output {
    margin-top: 20px;
    background: #fff;
    padding: 10px;
    border-radius: 4px;
    height: 150px;
    overflow: auto;
	color:black;
    font-family: monospace;
    font-size: 12px;
    border: 1px solid #ddd;
  }
  
  .temp-connection {
    position: absolute;
    height: 2px;
    background: #555;
    transform-origin: 0 0;
    z-index: 100;
    pointer-events: none;
  }
  
  .tab-container {
    display: flex;
    margin-bottom: 10px;
  }
  
  .tab {
    padding: 5px 10px;
    background: #ddd;
    cursor: pointer;
    margin-right: 5px;
    border-radius: 3px 3px 0 0;
  }
  
  .tab.active {
    background: #25D366;
    color: white;
  }
  
  .tab-content {
    display: none;
  }
  
  .tab-content.active {
    display: block;
  }
</style>
</head>
<body>

<div id="sidebar">
  <h2>Nodes</h2>
  <div class="node-item" draggable="true" data-type="start">Start</div>
  <div class="node-item" draggable="true" data-type="message">Message</div>
  <div class="node-item" draggable="true" data-type="question">Question</div>
  <div class="node-item" draggable="true" data-type="buttons">Quick Replies</div>
  <div class="node-item" draggable="true" data-type="cta">Call to Action</div>
  <div class="node-item" draggable="true" data-type="product">Product</div>
  <div class="node-item" draggable="true" data-type="appointment">Appointment</div>
  <div class="node-item" draggable="true" data-type="end">End</div>
  <button id="saveBtn">Save Flow</button>
  <div id="output">Flow JSON will appear here...</div>
</div>

<div id="canvas"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const canvas = document.getElementById('canvas');
  const saveBtn = document.getElementById('saveBtn');
  const output = document.getElementById('output');
  
  let nodes = [];
  let connections = [];
  let nextNodeId = 1;
  let draggedNode = null;
  let isDragging = false;
  let offsetX = 0, offsetY = 0;
  let isConnecting = false;
  let connectionSource = null;
  let tempConnection = null;

  // Initialize with a start node
  addNode('start', 50, 50);

  // Make sidebar nodes draggable
  document.querySelectorAll('.node-item').forEach(item => {
    item.addEventListener('dragstart', function(e) {
      e.dataTransfer.setData('type', this.dataset.type);
    });
  });

  // Handle drop on canvas
  canvas.addEventListener('dragover', function(e) {
    e.preventDefault();
  });

  canvas.addEventListener('drop', function(e) {
    e.preventDefault();
    const type = e.dataTransfer.getData('type');
    if (!type) return;
    
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    addNode(type, x, y);
  });

  // Add a new node to the canvas
  function addNode(type, x, y) {
    // Check for duplicate start/end nodes
    if (type === 'start' && nodes.some(n => n.type === 'start')) {
      alert('Only one start node allowed');
      return;
    }
    if (type === 'end' && nodes.some(n => n.type === 'end')) {
      alert('Only one end node allowed');
      return;
    }

    const node = {
      id: 'node_' + nextNodeId++,
      type,
      x,
      y,
      content: getDefaultContent(type),
      buttons: type === 'question' ? [
        {id: 'yes', text: 'Yes'}, 
        {id: 'no', text: 'No'}
      ] : [],
      url: type === 'cta' ? '' : undefined,
      price: type === 'product' ? '' : undefined,
      description: type === 'product' ? '' : undefined,
      imageUrl: type === 'product' ? '' : undefined,
      appointment: type === 'appointment' ? {
        title: '',
        description: '',
        dates: []
      } : undefined
    };
    
    nodes.push(node);
    render();
  }

  function getDefaultContent(type) {
    switch(type) {
      case 'start': return 'Conversation starts here';
      case 'message': return 'Type your message here';
      case 'question': return 'Ask your question here';
      case 'buttons': return 'Please select an option:';
      case 'cta': return 'Click the button below:';
      case 'product': return 'Product details:';
      case 'appointment': return 'Book an appointment:';
      case 'end': return 'Conversation ends here';
      default: return '';
    }
  }

  // Render all nodes and connections
  function render() {
    canvas.innerHTML = '';
    
    // Create connections first (so they appear behind nodes)
    connections.forEach(conn => {
      const fromNode = nodes.find(n => n.id === conn.from);
      const toNode = nodes.find(n => n.id === conn.to);
      
      if (fromNode && toNode) {
        if (conn.buttonId) {
          // Button connection
          drawButtonConnection(fromNode, toNode, conn.buttonId);
        } else {
          // Node connection
          drawNodeConnection(fromNode, toNode);
        }
      }
    });
    
    // Create nodes
    nodes.forEach(node => {
      createNodeElement(node);
    });
    
    // Remove temporary connection if not connecting
    if (!isConnecting && tempConnection) {
      tempConnection.remove();
      tempConnection = null;
    }
  }

  // Create a node DOM element
  function createNodeElement(node) {
    const nodeEl = document.createElement('div');
    nodeEl.className = 'node';
    nodeEl.style.left = node.x + 'px';
    nodeEl.style.top = node.y + 'px';
    nodeEl.dataset.id = node.id;
    
    // Node header
    const header = document.createElement('div');
    header.className = 'node-header';
    header.innerHTML = `<span>${capitalizeFirstLetter(node.type)}</span>`;
    
    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-btn';
    removeBtn.innerHTML = '×';
    removeBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      removeNode(node.id);
    });
    header.appendChild(removeBtn);
    nodeEl.appendChild(header);
    
    // Node content
    const content = document.createElement('div');
    content.className = 'node-content';
    
    if (['message', 'question', 'cta', 'buttons'].includes(node.type)) {
      const textarea = document.createElement('textarea');
      textarea.value = node.content;
      textarea.placeholder = 'Enter your text here...';
      textarea.addEventListener('input', function() {
        node.content = this.value;
      });
      content.appendChild(textarea);
    }
    
    // Add URL field for CTA
    if (node.type === 'cta') {
      const urlLabel = document.createElement('label');
      urlLabel.textContent = 'URL';
      const urlInput = document.createElement('input');
      urlInput.type = 'text';
      urlInput.value = node.url || '';
      urlInput.placeholder = 'https://example.com';
      urlInput.addEventListener('input', function() {
        node.url = this.value;
      });
      content.appendChild(urlLabel);
      content.appendChild(urlInput);
    }
    
    // Product node fields
    if (node.type === 'product') {
      const tabs = document.createElement('div');
      tabs.className = 'tab-container';
      
      const tab1 = document.createElement('div');
      tab1.className = 'tab active';
      tab1.textContent = 'Details';
      tab1.addEventListener('click', function() {
        setActiveTab(this, 'details');
      });
      
      const tab2 = document.createElement('div');
      tab2.className = 'tab';
      tab2.textContent = 'Buttons';
      tab2.addEventListener('click', function() {
        setActiveTab(this, 'buttons');
      });
      
      tabs.appendChild(tab1);
      tabs.appendChild(tab2);
      content.appendChild(tabs);
      
      // Details tab
      const detailsTab = document.createElement('div');
      detailsTab.className = 'tab-content active';
      detailsTab.id = 'details';
      
      const nameLabel = document.createElement('label');
      nameLabel.textContent = 'Product Name';
      const nameInput = document.createElement('input');
      nameInput.type = 'text';
      nameInput.value = node.content || '';
      nameInput.addEventListener('input', function() {
        node.content = this.value;
      });
      
      const priceLabel = document.createElement('label');
      priceLabel.textContent = 'Price';
      const priceInput = document.createElement('input');
      priceInput.type = 'text';
      priceInput.value = node.price || '';
      priceInput.placeholder = '$0.00';
      priceInput.addEventListener('input', function() {
        node.price = this.value;
      });
      
      const descLabel = document.createElement('label');
      descLabel.textContent = 'Description';
      const descTextarea = document.createElement('textarea');
      descTextarea.value = node.description || '';
      descTextarea.placeholder = 'Product description...';
      descTextarea.addEventListener('input', function() {
        node.description = this.value;
      });
      
      const imageLabel = document.createElement('label');
      imageLabel.textContent = 'Image URL';
      const imageInput = document.createElement('input');
      imageInput.type = 'text';
      imageInput.value = node.imageUrl || '';
      imageInput.placeholder = 'https://example.com/image.jpg';
      imageInput.addEventListener('input', function() {
        node.imageUrl = this.value;
      });
      
      detailsTab.appendChild(nameLabel);
      detailsTab.appendChild(nameInput);
      detailsTab.appendChild(priceLabel);
      detailsTab.appendChild(priceInput);
      detailsTab.appendChild(descLabel);
      detailsTab.appendChild(descTextarea);
      detailsTab.appendChild(imageLabel);
      detailsTab.appendChild(imageInput);
      content.appendChild(detailsTab);
      
      // Buttons tab
      const buttonsTab = document.createElement('div');
      buttonsTab.className = 'tab-content';
      buttonsTab.id = 'buttons';
      
      const buttonList = document.createElement('div');
      buttonList.className = 'button-list';
      
      if (!node.buttons) node.buttons = [];
      
      node.buttons.forEach((button, index) => {
        const buttonItem = document.createElement('div');
        buttonItem.className = 'button-item';
        
        const input = document.createElement('input');
        input.type = 'text';
        input.value = button.text || '';
        input.placeholder = 'Button text';
        input.addEventListener('input', function() {
          button.text = this.value;
        });
        
        const removeButton = document.createElement('button');
        removeButton.className = 'remove-btn';
        removeButton.innerHTML = '×';
        removeButton.addEventListener('click', function() {
          // Remove connections for this button
          connections = connections.filter(c => !(c.from === node.id && c.buttonId === button.id));
          node.buttons.splice(index, 1);
          render();
        });
        
        buttonItem.appendChild(input);
        buttonItem.appendChild(removeButton);
        buttonList.appendChild(buttonItem);
        
        // Add connector for each button
        const buttonConnector = document.createElement('div');
        buttonConnector.className = 'button-connector';
        buttonConnector.style.top = `${20 + index * 30}px`;
        buttonConnector.dataset.nodeId = node.id;
        buttonConnector.dataset.buttonId = button.id;
        buttonConnector.title = 'Drag to connect this button';
        
        buttonConnector.addEventListener('mousedown', function(e) {
          e.stopPropagation();
          startConnection(node.id, button.id);
        });
        
        buttonList.appendChild(buttonConnector);
      });
      
      const addButton = document.createElement('button');
      addButton.className = 'btn btn-blue';
      addButton.textContent = '+ Add Button';
      addButton.addEventListener('click', function() {
        node.buttons.push({ 
          id: `btn_${Math.random().toString(36).substr(2, 8)}`,
          text: `Option ${node.buttons.length + 1}`
        });
        render();
      });
      
      buttonsTab.appendChild(buttonList);
      buttonsTab.appendChild(addButton);
      content.appendChild(buttonsTab);
    }
    
    // Appointment node fields
    if (node.type === 'appointment') {
      const textarea = document.createElement('textarea');
      textarea.value = node.content;
      textarea.placeholder = 'Enter appointment message...';
      textarea.addEventListener('input', function() {
        node.content = this.value;
      });
      content.appendChild(textarea);
      
      const titleLabel = document.createElement('label');
      titleLabel.textContent = 'Title';
      const titleInput = document.createElement('input');
      titleInput.type = 'text';
      titleInput.value = node.appointment.title || '';
      titleInput.placeholder = 'Appointment title';
      titleInput.addEventListener('input', function() {
        node.appointment.title = this.value;
      });
      
      const descLabel = document.createElement('label');
      descLabel.textContent = 'Description';
      const descTextarea = document.createElement('textarea');
      descTextarea.value = node.appointment.description || '';
      descTextarea.placeholder = 'Appointment description...';
      descTextarea.addEventListener('input', function() {
        node.appointment.description = this.value;
      });
      
      const datesLabel = document.createElement('label');
      datesLabel.textContent = 'Available Dates (comma separated)';
      const datesInput = document.createElement('input');
      datesInput.type = 'text';
      datesInput.value = node.appointment.dates.join(', ') || '';
      datesInput.placeholder = 'e.g. 2023-01-01, 2023-01-02';
      datesInput.addEventListener('input', function() {
        node.appointment.dates = this.value.split(',').map(date => date.trim());
      });
      
      content.appendChild(titleLabel);
      content.appendChild(titleInput);
      content.appendChild(descLabel);
      content.appendChild(descTextarea);
      content.appendChild(datesLabel);
      content.appendChild(datesInput);
    }
    
    // Add buttons for question, buttons, and CTA nodes
    if (['question', 'buttons', 'cta'].includes(node.type)) {
      const buttonList = document.createElement('div');
      buttonList.className = 'button-list';
      
      node.buttons.forEach((button, index) => {
        const buttonItem = document.createElement('div');
        buttonItem.className = 'button-item';
        
        const input = document.createElement('input');
        input.type = 'text';
        input.value = button.text || '';
        input.placeholder = 'Button text';
        input.addEventListener('input', function() {
          button.text = this.value;
        });
        
        const removeButton = document.createElement('button');
        removeButton.className = 'remove-btn';
        removeButton.innerHTML = '×';
        removeButton.addEventListener('click', function() {
          // Remove connections for this button
          connections = connections.filter(c => !(c.from === node.id && c.buttonId === button.id));
          node.buttons.splice(index, 1);
          render();
        });
        
        buttonItem.appendChild(input);
        buttonItem.appendChild(removeButton);
        buttonList.appendChild(buttonItem);
        
        // Add connector for each button
        const buttonConnector = document.createElement('div');
        buttonConnector.className = 'button-connector';
        buttonConnector.style.top = `${20 + index * 30}px`;
        buttonConnector.dataset.nodeId = node.id;
        buttonConnector.dataset.buttonId = button.id;
        buttonConnector.title = 'Drag to connect this button';
        
        buttonConnector.addEventListener('mousedown', function(e) {
          e.stopPropagation();
          startConnection(node.id, button.id);
        });
        
        buttonList.appendChild(buttonConnector);
      });
      
      // Limit to 2 buttons for question and CTA nodes
      if ((node.type === 'question' || node.type === 'cta') && node.buttons.length >= 2) {
        // Don't show add button
      } else {
        const addButton = document.createElement('button');
        addButton.className = 'btn btn-blue';
        addButton.textContent = '+ Add Button';
        addButton.addEventListener('click', function() {
          node.buttons.push({ 
            id: `btn_${Math.random().toString(36).substr(2, 8)}`,
            text: `Option ${node.buttons.length + 1}`
          });
          render();
        });
        buttonList.appendChild(addButton);
      }
      
      content.appendChild(buttonList);
    }
    
    nodeEl.appendChild(content);
    
    // Output connector (except for end node)
    if (node.type !== 'end') {
      const outputConnector = document.createElement('div');
      outputConnector.className = 'output-connector';
      outputConnector.dataset.nodeId = node.id;
      outputConnector.title = 'Drag to connect this node';
      
      outputConnector.addEventListener('mousedown', function(e) {
        e.stopPropagation();
        startConnection(node.id);
      });
      
      nodeEl.appendChild(outputConnector);
    }
    
    // Node dragging implementation
    nodeEl.addEventListener('mousedown', function(e) {
      // Don't start drag if clicking on inputs or buttons
      if (e.target.tagName === 'TEXTAREA' || 
          e.target.tagName === 'INPUT' || 
          e.target.classList.contains('remove-btn') ||
          e.target.classList.contains('btn') ||
          e.target.classList.contains('output-connector') ||
          e.target.classList.contains('button-connector')) {
        return;
      }
      
      isDragging = true;
      draggedNode = node;
      
      // Calculate offset from mouse to node position
      const rect = nodeEl.getBoundingClientRect();
      offsetX = e.clientX - rect.left;
      offsetY = e.clientY - rect.top;
      
      // Make node appear on top while dragging
      nodeEl.style.zIndex = '100';
      
      // Prevent text selection during drag
      e.preventDefault();
    });
    
    canvas.appendChild(nodeEl);
  }

  function setActiveTab(tabElement, tabId) {
    // Remove active class from all tabs and contents
    const tabs = tabElement.parentElement.querySelectorAll('.tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    const contents = tabElement.parentElement.parentElement.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.remove('active'));
    
    // Add active class to clicked tab and corresponding content
    tabElement.classList.add('active');
    document.getElementById(tabId).classList.add('active');
  }

  // Start creating a connection
  function startConnection(sourceNodeId, buttonId = null) {
    isConnecting = true;
    connectionSource = {
      nodeId: sourceNodeId,
      buttonId: buttonId
    };
    
    // Create temporary connection line
    tempConnection = document.createElement('div');
    tempConnection.className = 'temp-connection';
    canvas.appendChild(tempConnection);
    
    // Update connection line position as mouse moves
    document.addEventListener('mousemove', updateTempConnection);
    
    // Complete connection on mouseup
    document.addEventListener('mouseup', completeConnection, { once: true });
  }

  // Update temporary connection line during creation
  function updateTempConnection(e) {
    if (!isConnecting || !connectionSource) return;
    
    const sourceNode = nodes.find(n => n.id === connectionSource.nodeId);
    if (!sourceNode) return;
    
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    
    const startX = sourceNode.x + 250;
    const startY = connectionSource.buttonId 
  ? sourceNode.y + 70 + (sourceNode.buttons.findIndex(b => b.id === connectionSource.buttonId) * 30)
  : sourceNode.y + 40;

    
    const length = Math.sqrt(Math.pow(mouseX - startX, 2) + Math.pow(mouseY - startY, 2));
    const angle = Math.atan2(mouseY - startY, mouseX - startX) * 180 / Math.PI;
    
    tempConnection.style.width = length + 'px';
    tempConnection.style.left = startX + 'px';
    tempConnection.style.top = startY + 'px';
    tempConnection.style.transform = 'rotate(' + angle + 'deg)';
  }

  // Complete the connection
  function completeConnection(e) {
    if (!isConnecting || !connectionSource) return;
    
    // Find if we're hovering over a node
    const targetElement = document.elementFromPoint(e.clientX, e.clientY);
    const targetNodeEl = targetElement?.closest('.node');
    
    if (targetNodeEl) {
      const targetNodeId = targetNodeEl.dataset.id;
      const targetNode = nodes.find(n => n.id === targetNodeId);
      
      if (targetNode && targetNode.id !== connectionSource.nodeId) {
        // Check if connection already exists
        const existingConnection = connections.find(conn => 
          conn.from === connectionSource.nodeId && 
          conn.to === targetNode.id &&
          conn.buttonId === connectionSource.buttonId);
        
        if (!existingConnection) {
          connections.push({
            from: connectionSource.nodeId,
            to: targetNode.id,
            buttonId: connectionSource.buttonId
          });
        }
      }
    }
    
    // Clean up
    document.removeEventListener('mousemove', updateTempConnection);
    if (tempConnection) {
      tempConnection.remove();
      tempConnection = null;
    }
    isConnecting = false;
    connectionSource = null;
    
    render();
  }

  // Draw connection between nodes
  function drawNodeConnection(fromNode, toNode) {
    const startX = fromNode.x + 250;
    const startY = fromNode.y + 40;
    const endX = toNode.x;
    const endY = toNode.y + 40;
    
    const line = document.createElement('div');
    line.className = 'connection';
    
    const length = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
    const angle = Math.atan2(endY - startY, endX - startX) * 180 / Math.PI;
    
    line.style.width = length + 'px';
    line.style.left = startX + 'px';
    line.style.top = startY + 'px';
    line.style.transform = 'rotate(' + angle + 'deg)';
    
    canvas.appendChild(line);
  }

  // Draw connection from button to node
  function drawButtonConnection(fromNode, toNode, buttonId) {
    const buttonIndex = fromNode.buttons.findIndex(b => b.id === buttonId);
    const startX = fromNode.x + 250;
    const startY = fromNode.y + 70 + (buttonIndex * 30);
    const endX = toNode.x;
    const endY = toNode.y + 40;
    
    const line = document.createElement('div');
    line.className = 'button-connection';
    
    const length = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
    const angle = Math.atan2(endY - startY, endX - startX) * 180 / Math.PI;
    
    line.style.width = length + 'px';
    line.style.left = startX + 'px';
    line.style.top = startY + 'px';
    line.style.transform = 'rotate(' + angle + 'deg)';
    
    canvas.appendChild(line);
  }

  // Remove a node and its connections
  function removeNode(nodeId) {
    if (confirm('Delete this node and all its connections?')) {
      nodes = nodes.filter(n => n.id !== nodeId);
      connections = connections.filter(conn => 
        conn.from !== nodeId && conn.to !== nodeId
      );
      render();
    }
  }

  // Handle node dragging
  document.addEventListener('mousemove', function(e) {
    if (isDragging && draggedNode) {
      const rect = canvas.getBoundingClientRect();
      draggedNode.x = e.clientX - rect.left - offsetX;
      draggedNode.y = e.clientY - rect.top - offsetY;
      render();
    }
  });

  document.addEventListener('mouseup', function() {
    if (isDragging && draggedNode) {
      const nodeEl = document.querySelector(`[data-id="${draggedNode.id}"]`);
      if (nodeEl) {
        nodeEl.style.zIndex = '1';
      }
    }
    isDragging = false;
    draggedNode = null;
  });

  // Save flow in WhatsApp API compatible format
  saveBtn.addEventListener('click', function() {
    const flow = {
      version: "1.0",
      nodes: nodes.map(node => ({
        id: node.id,
        type: node.type,
        content: node.content,
        ...(node.buttons && node.buttons.length > 0 ? { 
          buttons: node.buttons.map(btn => ({
            id: btn.id,
            text: btn.text,
            connectedTo: connections.find(c => c.from === node.id && c.buttonId === btn.id)?.to || null
          }))
        } : {}),
        ...(node.type === 'cta' ? { url: node.url } : {}),
        ...(node.type === 'product' ? { 
          product: {
            name: node.content,
            price: node.price,
            description: node.description,
            imageUrl: node.imageUrl
          }
        } : {}),
        ...(node.type === 'appointment' ? { 
          appointment: node.appointment 
        } : {})
      })),
      connections: connections.map(conn => ({
        from: conn.from,
        to: conn.to,
        ...(conn.buttonId ? { buttonId: conn.buttonId } : {})
      }))
    };
    
    output.textContent = JSON.stringify(flow, null, 2);
    console.log('Flow saved:', flow);
    alert('Flow saved! Check the output box below or console for the JSON.');
  });

  // Utility function
  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
});
</script>
</body>
</html>