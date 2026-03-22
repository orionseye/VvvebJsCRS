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

function wrapInGrid(html, colSize = "column full") {
    return `<div class="row top-parent-row">
        <div class="${colSize}">
            ${html}
        </div>
    </div>`;
}

/************************************************
 VvvebJS's native Action buttons trigger & its popup
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

/*****************************************************
  Create custom action-buttons for top-row, row, column
*****************************************************/

function createActionToolbar(containerId, buttons) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const actionsDiv = document.createElement('div');
    actionsDiv.id = containerId + '-actions';
    actionsDiv.className = 'rowCol-box-actions';
    
    // Gear trigger (always visible)
    const trigger = document.createElement('a');
    trigger.href = '';
    trigger.title = 'More actions';
    trigger.innerHTML = '<i class="la la-cog me-1"></i>';
    trigger.className = 'rowCol-action-trigger';
    
    // Popup container (hidden by default)
    const popup = document.createElement('div');
    popup.id = containerId + '-popup';
    popup.className = 'rowCol-action-popup';
    
    const buttonConfigs = {
        'up': { icon: 'la la-arrow-up', title: 'Move up' },
        'down': { icon: 'la la-arrow-down', title: 'Move down' },
        'left': { icon: 'la la-arrow-left', title: 'Move left' },
        'right': { icon: 'la la-arrow-right', title: 'Move right' },
        'clone': { icon: 'icon-copy-outline', title: 'Clone' }
    };
    
    buttons.forEach(btnType => {
        if (btnType === 'delete') return; // Skip delete in popup
        const config = buttonConfigs[btnType];
        if (config) {
            const btn = document.createElement('a');
            btn.id = containerId + '-' + btnType + '-btn';
            btn.href = '';
            btn.title = config.title;
            btn.innerHTML = `<i class="${config.icon}"></i>`;
            popup.appendChild(btn);
        }
    });
    
    // Delete button (always visible)
    const deleteBtn = document.createElement('a');
    deleteBtn.id = containerId + '-delete-btn';
    deleteBtn.href = '';
    deleteBtn.title = 'Delete';
    deleteBtn.innerHTML = '<i class="icon-trash-outline"></i>';
    deleteBtn.className = 'rowCol-delete-btn';
    
    // Toggle popup on gear click
	trigger.addEventListener('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		popup.classList.toggle('show');
	});
    
    // Close popup when clicking elsewhere
	document.addEventListener('click', function(e) {
		if (!trigger.contains(e.target) && !popup.contains(e.target)) {
			popup.classList.remove('show');
		}
	});
    
    actionsDiv.appendChild(trigger);
    actionsDiv.appendChild(popup);
    actionsDiv.appendChild(deleteBtn);
    container.appendChild(actionsDiv);
}



/*****************************************************
  Attach Action Handlers for row + column action-buttons
  by using VvvebeJS's native buttons (already in builder.js)
*****************************************************/
function attachActionHandlers() {
    function delegateToExisting(targetSelector, buttonId, shouldRestore = true) {
        return function(e) {
            e.preventDefault();
            e.stopPropagation();
            let target = Vvveb.Builder.selectedEl.closest(targetSelector);
            if (target) {
                let original = Vvveb.Builder.selectedEl;
                Vvveb.Builder.selectedEl = target;
                document.getElementById(buttonId).click();
                if (shouldRestore) Vvveb.Builder.selectedEl = original;
            }
        };
    }

    // Map box ID prefix → target selector
    const selectorMap = {
        'top-parent-row-box': '.top-parent-row',
        'row-box': '.row',
        'column-box': '.column'
    };

    // Auto-detect all buttons matching pattern: {boxId}-{action}-btn
    document.querySelectorAll('[id$="-btn"]').forEach(btn => {
        const match = btn.id.match(/^(.+?)-(up|down|left|right|clone|delete)-btn$/);
        if (match) {
            const [, boxId, action] = match;
            const selector = selectorMap[boxId];
            if (selector) {
                btn.addEventListener("click", delegateToExisting(selector, `${action}-btn`, action !== 'delete'));
            }
        }
    });
}