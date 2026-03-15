/*****************************************************
  Editor specific behaviour whether builder 
  has content or not
*****************************************************/
function insertDummyButton() {
	if (window.FrameDocument && window.FrameDocument.body) {
		if (window.FrameDocument.getElementById('htmlArea').children.length === 0) {
						
			const btn = window.FrameDocument.createElement('button');
			btn.className = 'empty-state-btn';
			btn.textContent = 'Oh, nothing here. + Click me to add your first content +';
			
			// Style it as a big block, like in Contenbuilder.js
			btn.style.cssText = "display: block; width: 70%; height: 200px; margin: 0 auto; font-family: sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; justify-content: center; align-items: center; color: #333; border: 1px dashed rgba(169, 169, 169, 0.8); background: rgba(255, 255, 255, 0.5); cursor: pointer; transition: all ease 0.3s; outline: none !important;";
			
			// on click, we also need to hide VvvbJS default '+' button end action options
			// this is done with pure CSS, when body has class 'no-content'. 
			// see the style declarations at editor.css
			btn.onclick = function(e) {
				e.stopPropagation();
				// Set htmlArea to be now the selected element, since button is gone and canvas empty
				Vvveb.Builder.selectNode(window.FrameDocument.getElementById('htmlArea'));
				// set radio button to 'inside' mode .. only for first insert on our blank canvas
				document.querySelector('#add-section-insert-mode-after').checked = false;
				document.querySelector('#add-section-insert-mode-inside').checked = true;
				// Trigger the native '+' button click
				document.getElementById("add-section-btn").click();
				
				// After successful insertion (see builder.js vvveb.component.added event)
				document.addEventListener('vvveb.component.added', function() {
					// Delete dummy button first.. canvas is empty now
					btn.remove();
					document.body.classList.remove('no-content');// remove no-content class from body
					document.querySelector('#add-section-insert-mode-after').checked = true;
					document.querySelector('#add-section-insert-mode-inside').checked = false;
					
					// New node is actually inserted after the btn, gets auto-selected for editing
					// When btn gets removed, new node jumps to top and leaves its selection at the bottom.
					// That's why we force selection box to update position to new inserted node (jump also up to its node).
					setTimeout(() => {
						Vvveb.Builder.selectNode(window.FrameDocument.getElementById('htmlArea').lastElementChild);
					}, 50);
					
				}, { once: true });					
			};
			
			try {
				window.FrameDocument.getElementById('htmlArea').appendChild(btn);
			} catch(e) {
				alert('Editor error: htmlArea wrapper missing - please refresh');
			}
		}
	}
}

// Watch for deletions in the iframe htmlArea
// When all elements are deleted (htmlArea empty), revert to empty state.
// We use my CustomEvent('vvveb.component.removed')) -> builder.js aprox. line 1848
// - Show no-content class in body
// - Select htmlArea as insertion target
// - Set insert mode to 'inside'
document.addEventListener('vvveb.component.removed', function() {
	setTimeout(function() {
		if (window.FrameDocument.getElementById('htmlArea').children.length === 0) {
			document.body.classList.add('no-content');
			document.body.classList.remove('has-content');// in some cases 'has-content' persists, so we remove it.
			
			Vvveb.Builder.selectNode(window.FrameDocument.getElementById('htmlArea'));
			
			insertDummyButton();
		}
	}, 50);
});
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