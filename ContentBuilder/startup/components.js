Vvveb.ComponentsGroup['Dimi'] = [
    "dimi/flex-container"
];

Vvveb.Components.add("dimi/flex-container", {
    name: "Flex Container",
    image: "../../startup/preview/000-02.png",
    html: `
	<div style="display: flex; flex-direction: column;">
	
		<!-- Test 1: Equal auto-flex columns (most common) -->
		<div class="IncrDecr-flag" style="display: flex; flex-direction: row;">
			<div style="flex: 1; padding: 20px; background: #ffcccc; border: 1px solid #ff9999;">Column 1</div>
			<div style="flex: 1; padding: 20px; background: #ccffcc; border: 1px solid #99ff99;">Column 2</div>
			<div style="flex: 1; padding: 20px; background: #ccccff; border: 1px solid #9999ff;">Column 3</div>
		</div>

		<!-- Test 2: Bootstrap-style (explicit percentages) -->
		<div class="IncrDecr-flag" style="display: flex; flex-direction: row;">
			<div style="flex: 1; padding: 20px; background: #ffcccc; border: 1px solid #ff9999;">Column 1 (50%)</div>
			<div style="flex: 1; padding: 20px; background: #ccffcc; border: 1px solid #99ff99;">Column 2 (50%)</div>
		</div>

		<!-- Test 3: Mixed (one explicit, one auto) -->
		<div class="IncrDecr-flag" style="display: flex; flex-direction: row;">
			<div style="flex: 0 0 33.33%; padding: 20px; background: #ffcccc; border: 1px solid #ff9999;">Column 1 (33%)</div>
			<div style="flex: 1; padding: 20px; background: #ccffcc; border: 1px solid #99ff99;">Column 2 (auto)</div>
		</div>
		
	</div>
    `,
    
    afterDrop: function(node) {
        const body = Vvveb.Builder.frameBody;
        const existingHandler = body.querySelector("#flex-badge-handler");
        
        if (!existingHandler) {
            const script = document.createElement('script');
            script.id = 'flex-badge-handler';
            script.type = 'text/javascript';
            script.text = `
                document.body.addEventListener('click', function(e) {
                    const parent = e.target.closest('.IncrDecr-flag');
                    document.querySelectorAll('.flex-parent-badge').forEach(b => b.remove());
                    if (parent && e.target !== parent) {
                        if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
                        const badge = document.createElement('div');
                        badge.className = 'flex-parent-badge';
                        badge.innerText = '⇖ Select Row';
                        badge.style.cssText = "position: absolute; bottom: 5px; right: 5px; background: #007bff; color: white; font-size: 10px; padding: 2px 6px; border-radius: 3px; cursor: pointer; z-index: 1000; opacity: 0.8; font-family: sans-serif;";
                        badge.onclick = function(event) {
                            event.stopPropagation();
                            parent.click();
                            badge.remove();
                        };
                        parent.appendChild(badge);
                    }
                }, true);
            `;
            body.appendChild(script);
        }
        return node;
    }
});