/************************************************
 Inject htmlArea as our main wrapper after body. 
 Use it to fetch html content and to style the builder's inner container.
************************************************/
function createHtmlAreaWrapper() {
    let wrapper = window.FrameDocument.createElement('div');
    wrapper.id = 'htmlArea';
    wrapper.classList = 'container';
    //wrapper.style.cssText = "width: 75%; margin: 0 auto; background: white; min-height: 600px; transition: all 0.3s ease;";
	
    // Move all existing body children into wrapper
    while (window.FrameDocument.body.firstChild) {
        wrapper.appendChild(window.FrameDocument.body.firstChild);
    }
    
    window.FrameDocument.body.appendChild(wrapper);
    return wrapper;
}


/************************************************
 Helper to wrap components in row/col structure
************************************************/

function wrapInGrid(html, colSize = "col-md-12") {
    return `<div class="row">
        <div class="${colSize}">
            ${html}
        </div>
    </div>`;
}

/************************************************
 Action buttons trigger & its popup
************************************************/
// Toggle action buttons on trigger click
document.getElementById('action-trigger').addEventListener('click', function(e) {
	const buttons = document.getElementById('action-buttons');
	buttons.classList.toggle('show');
	e.stopPropagation();
});

// Close when clicking outside (on main document)
document.addEventListener('click', function(e) {
	const buttons = document.getElementById('action-buttons');
	const trigger = document.getElementById('action-trigger');
	
	if (!trigger.contains(e.target) && !buttons.contains(e.target)) {
		buttons.classList.remove('show');
	}
});

// Close when selecting a different node
let lastSelectedNode = null;
const originalSelectNode = Vvveb.Builder.selectNode;

Vvveb.Builder.selectNode = function(node) {
	// Only close popup if selecting a DIFFERENT node
	if (node !== lastSelectedNode) {
		const buttons = document.getElementById('action-buttons');
		if (buttons) {
			buttons.classList.remove('show');
		}
		lastSelectedNode = node;
	}
	
	// Call original function
	originalSelectNode.call(this, node);
};