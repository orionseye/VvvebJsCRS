Vvveb.ComponentsGroup['Dimi'] = [
    "dimi/flex-container"
];

Vvveb.Components.add("dimi/flex-container", {
    name: "Flex Container",
    image: "../../startup/preview/000-02.png",
    html: `
	<div class="flex-box-builder" style="display: flex; flex-direction: column;">
	
		<!-- Test 1: Equal auto-flex columns (most common) -->
		<div class="IncrDecr-flag" style="display: flex; flex-direction: row;">
			<div style="flex: 1; padding: 20px; background: #81A6C6;">Column 1</div>
			<div style="flex: 1; padding: 20px; background: #AACDDC;">Column 2</div>
			<div style="flex: 1; padding: 20px; background: #F3E3D0;">Column 3</div>
		</div>

		<!-- Test 2: Bootstrap-style (explicit percentages) -->
		<div class="IncrDecr-flag" style="display: flex; flex-direction: row;">
			<div style="flex: 1; padding: 20px; background: #F3E3D0;">Column 1 (50%)</div>
			<div style="flex: 1; padding: 20px; background: #81A6C6;">Column 2 (50%)</div>
		</div>

		<!-- Test 3: Mixed (one explicit, one auto) -->
		<div class="IncrDecr-flag" style="display: flex; flex-direction: row;">
			<div style="flex: 0 0 33.33%; padding: 20px; background: #AACDDC;">Column 1 (33%)</div>
			<div style="flex: 1; padding: 20px; background: #F3E3D0;">Column 2 (auto)</div>
		</div>
	  
	</div>
    `,
	
	/*afterDrop: function(node) {
        const body = Vvveb.Builder.frameBody;
        const existingHandler = body.querySelector("#flex-badge-handler");
        
        if (!existingHandler) {
            const script = document.createElement('script');
            script.id = 'flex-badge-handler';
            script.type = 'text/javascript';
            script.text = `

            `;
            body.appendChild(script);
        }
        return node;
    }
	*/
});