<?php
/*   CLONED from standalone_service_nonchargeable_options.php

NEW FILES CREATED & CHANGES OVERALL:
+ \nat\setup\standalone\ContentBuilder\standalone_service_html_detail.php
+ \nat\setup\standalone\ContentBuilder\standalone_service_html_detail_db.php
+ \classcore\data\StandaloneServicesHtmlDetailData.php
+ NEW DB table "standalone_services_html_detail" cloned from "standalone_services_nonchargeable_options"
+ \ContentBuilder files
*/
include_once("session.php");
include_once(CLASSCORE."pages/StandaloneItemAdminMainPage.php");
include_once(CLASSCORE."data/StandaloneServicesHtmlDetailData.php"); 
include_once(LIB."Utils.php");

$page= new  StandaloneItemAdminMainPage();

$serviceID=-1;
$serviceID=$page->getService();
$_SESSION['sID'] = $serviceID;	// needed for saveimage.php

		$oOo= new StandaloneServiceHtmlDetailData();
		$oOo->startIterator(" AND serviceID=" . $serviceID . "");
			while($oOo->fetchNext($row))
			{
				// echo '<pre>'; print_r($row); echo '</pre>';
				$dbHtml = $row["html"];
			}

		if (!empty(trim($dbHtml))) {
			
			$actionValue = 'edit';
			$hasContent = true;
			
			// Create temp file only when there's content
			// Gets deleted once page has rendered with the desired html for the editor. See after Vvveb.Builder.init(..
			// Gets regenerated after save. See in standalone_service_html_detail_db.php, so we always have fresh content.
			// ...and gets again deleted.. etc
			$tempFile = '_temp/temp_' . $serviceID . '.html';
			file_put_contents($tempFile, $dbHtml);
			
		} else {
			
			$actionValue = 'add';
			$hasContent = false;
			
			// No temp file created
			// Create a photo directory in case none exists yet (takes also in consideration that no DB row exists yet! .. in case you deleted the folder for debbuging, but the DB row still exists and is empty)
			$destination = USER_DATA.'standalone'.SEP.$serviceID.SEP.'editor'.SEP;
			
			try {
				Utils::CreateDirectory($destination);
			} 
			catch (Exception $e) 
			{
				$err.="".$e->getMessage();
			}
		}

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <base href="">
    <title>VvvebJs</title>
    
    <link href="css/editor.css" rel="stylesheet">

	<style>
	/* on click of our dummy button, we hide VvvbJS default '+' button end action options */
	body.no-content #add-section-btn,
	body.no-content #select-actions {
		display: none !important;
	}
	</style>
  </head>
<body class="<?php echo $hasContent ? 'has-content' : 'no-content'; ?>">


	<div id="vvveb-builder">
				<div id="top-panel">
					<!-- img src="img/logo.png" alt="Vvveb" class="float-start" id="logo" -->
					
					<div class="btn-group float-start" role="group">
					  <button class="btn btn-light" title="Toggle file manager" id="toggle-file-manager-btn" data-vvveb-action="toggleFileManager" data-bs-toggle="button" aria-pressed="false">
						  <img src="libs/builder/icons/file-manager-layout.svg" width="18" height="18" alt="" role="presentation">
					  </button>

					  <button class="btn btn-light" title="Toggle left column" id="toggle-left-column-btn" data-vvveb-action="toggleLeftColumn" data-bs-toggle="button" aria-pressed="false">
						  <img src="libs/builder/icons/left-column-layout.svg" width="18" height="18" alt="" role="presentation">
					  </button>
					  
					  <button class="btn btn-light" title="Toggle right column" id="toggle-right-column-btn" data-vvveb-action="toggleRightColumn" data-bs-toggle="button" aria-pressed="false">
						  <img src="libs/builder/icons/right-column-layout.svg" width="18" height="18" alt="" role="presentation">
					  </button>
					</div>

					<div class="btn-group me-3" role="group">
					  <button class="btn btn-light" title="Undo (Ctrl/Cmd + Z)" id="undo-btn" data-vvveb-action="undo" data-vvveb-shortcut="ctrl+z">
						  <i class="la la-undo"></i>
					  </button>

            <button class="btn btn-light" title="Redo (Ctrl/Cmd + Shift + Y)" id="redo-btn" data-vvveb-action="redo" data-vvveb-shortcut="ctrl+shift+z">
              <i class="la la-undo la-flip-horizontal"></i>
					  </button>
					</div>
										
					
					<div class="btn-group me-3" role="group">
					  <button class="btn btn-light" title="Designer Mode (Free dragging)" id="designer-mode-btn" data-bs-toggle="button" aria-pressed="false" data-vvveb-action="setDesignerMode">
						  <i class="la la-hand-rock"></i>
					  </button>

					  <button class="btn btn-light" title="Preview" id="preview-btn" type="button" data-bs-toggle="button" aria-pressed="false" data-vvveb-action="preview">
                <i class="icon-eye-outline"></i>
					  </button>

					  <button class="btn btn-light" title="Fullscreen (F11)" id="fullscreen-btn" data-bs-toggle="button" aria-pressed="false" data-vvveb-action="fullscreen">
						  <i class="icon-expand-outline"></i>
					  </button>

					  <button class="btn btn-light active" title="Toggle navigator (Ctrl + Shift + N)" id="toggle-tree-list" data-bs-toggle="button" data-vvveb-action="toggleTreeList" aria-pressed="true">
						  <i class="icon-layers-outline"></i>
					  </button>

					  <button class="btn btn-light" title="Download" id="download-btn" data-vvveb-action="download" data-v-download="index.html">
						  <i class="la la-download"></i>
					  </button>

                      <button class="btn btn-light" title="Toggle right column" id="dimi-toggle-right-column-btn" data-vvveb-action="toggleRightColumn" data-bs-toggle="button" aria-pressed="false">
						  <i class="icon-settings-outline"></i>
					  </button>
					  
					</div>
					
								
					<div class="btn-group me-2 float-end" role="group">
					  <button class="btn btn-primary btn-sm btn-icon save-btn" title="Save (Ctrl + S)" id="btnSave" data-v-vvveb-shortcut="ctrl+e">

						<span class="loading d-none">
						<i class="icon-save-outline"></i>
						  <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true">
						  </span>
						  <span>Saving </span> ... </span>

						<span class="button-text">
						  <i class="icon-save-outline"></i> <span>Save page</span>
						</span>	

					  </button>
					</div>	

					  <div class="float-end me-3">

					    <button class="btn btn-light border-0 btn-sm btn-dark-mode" data-vvveb-action="darkMode">
							<!-- i class="la la-adjust la-lg"></i -->
							<i class="la la-sun"></i>
						</button>
						
					    <a href="#" class="btn btn-light btn-sm btn-preview-url" target="blank" title="View page">
							<i class="la la-external-link-alt"></i>
						</a>

					    <div class="btn-group responsive-btns" role="group">


		 			 <button id="mobile-view" data-view="mobile" class="btn btn-light"  title="Mobile view" data-vvveb-action="viewport">
						  <i class="la la-mobile"></i>
					  </button>

					  <button id="tablet-view"  data-view="tablet" class="btn btn-light"  title="Tablet view" data-vvveb-action="viewport">
						  <i class="la la-tablet"></i>
					  </button>
					  
					  <button id="desktop-view"  data-view="" class="btn btn-light"  title="Desktop view" data-vvveb-action="viewport">
						  <i class="la la-laptop"></i>
					  </button>
			
					<div class="percent">
					   <input type="number" id="zoom" value="100" step="10" min="10" max="100" class="form-control" data-vvveb-action="zoomChange" data-vvveb-on="change">

					</div>
					</div>
										
				</div>	
				
     				   </div>


				<div id="left-panel">
					  <div>
					  <div id="filemanager"> 
							<div class="header">
								<span class="text-secondary"><i class="la la-file la-lg"></i> Pages</span>

									<div class="btn-group responsive-btns float-end" role="group">
									  <button class="btn btn-outline-primary btn-sm btn-add" title="New page (Ctrl + Shift + P)" id="new-file-btn" data-vvveb-action="newPage" data-vvveb-shortcut="">
									<!-- <span>Add new</span> -->
									<i class="icon-add"></i>
									  </button>
									  
									  <!--  &ensp;
									  <button class="btn btn-link text-dark p-0"  title="Delete file" id="delete-file-btn" data-vvveb-action="deletePage" data-vvveb-shortcut="">
										  <i class="la la-trash"></i> <small>Delete</small>
									  </button> -->
									</div>

								</div>

								<div class="tree">
									<ol>
									</ol>
								</div>
					  </div>
					  
					  
					 <div class="drag-elements">
						
						<div class="header">
							<ul class="nav nav-tabs  nav-fill" id="elements-tabs" role="tablist">
							  <li class="nav-item sections-tab">
								<a class="nav-link active" id="sections-tab" data-bs-toggle="tab" href="#sections" role="tab" aria-controls="sections" aria-selected="true" title="Sections">
									<i class="icon-layers-outline"></i>
									<!-- img src="../../../js/vvvebjs/icons/list_group.svg" height="23" --> 
									<!-- div><small>Sections</small></div -->
								</a>
							  </li>
							  <li class="nav-item component-tab">
								<a class="nav-link" id="components-tab" data-bs-toggle="tab" href="#components-tabs" role="tab" aria-controls="components" aria-selected="false" title="Components">
									<i class="icon-cube-outline"></i>
									<!-- img src="../../../js/vvvebjs/icons/product.svg" height="23" --> 
									<!-- div><small>Components</small></div -->
								</a>
							  </li>
							  <!-- li class="nav-item sections-tab">
								<a class="nav-link" id="sections-tab" data-bs-toggle="tab" href="#sections" role="tab" aria-controls="sections" aria-selected="false" title="Sections"><img src="../../../js/vvvebjs/icons/list_group.svg" width="24" height="23"> <div><small>Sections</small></div></a>
							  </li -->
							  <li class="nav-item component-properties-tab d-none">
								<a class="nav-link" id="properties-tab" data-bs-toggle="tab" href="#properties" role="tab" aria-controls="properties" aria-selected="false" title="Properties">
									<i class="icon-settings-outline"></i>
									<!-- img src="../../../js/vvvebjs/icons/filters.svg" height="23"--> 
									<!-- div><small>Properties</small></div -->
								</a>
							  </li>
							  <li class="nav-item component-configuration-tab">
								<a class="nav-link" id="configuration-tab" data-bs-toggle="tab" href="#configuration" role="tab" aria-controls="configuration" aria-selected="false" title="Styles">
									<i class="icon-color-wand-outline"></i>
									<!-- img src="../../../js/vvvebjs/icons/filters.svg" height="23"--> 
									<!-- div><small>Properties</small></div -->
								</a>
							  </li>
							</ul>
					
							<div class="tab-content">
							  
							  
							  <div class="tab-pane show active sections" id="sections" role="tabpanel" aria-labelledby="sections-tab">
								  

										<ul class="nav nav-tabs nav-fill nav-underline  sections-tabs" id="sections-tabs" role="tablist">
										  <li class="nav-item content-tab">
											<a class="nav-link active" data-bs-toggle="tab" href="#sections-new-tab" role="tab" aria-controls="components" aria-selected="false">
												<i class="icon-albums-outline"></i> <div><span>Sections</span></div></a>
										  </li>
										  <li class="nav-item style-tab">
											<a class="nav-link" data-bs-toggle="tab" href="#sections-list" role="tab" aria-controls="sections" aria-selected="true">
												 <i class="icon-document-text-outline"></i><div><span>Page Sections</span></div></a>
										  </li>
										</ul>
								
										<div class="tab-content">
		
											 <div class="tab-pane" id="sections-list" data-section="style" role="tabpanel" aria-labelledby="style-tab">
												<div class="drag-elements-sidepane sidepane">
												  <div>
													<div class="sections-container p-4">
																													  
															<div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">&nbsp;
																			<div class="type">&nbsp;</div>
																		</div>
																	</div>
																</div>
															</div> 
															<div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">&nbsp;
																			<div class="type">&nbsp;</div>
																		</div>
																	</div>
																</div>
															</div> 
															<div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">&nbsp;
																			<div class="type">&nbsp;</div>
																		</div>
																	</div>
																</div>
															</div> 
															<!-- div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">welcome area
																			<div class="type">section</div>
																		</div>
																	</div>
																	<div class="buttons"> <a class="delete-btn" href="" title="Remove section"><i class="la la-trash text-danger"></i></a>
																		
																		<a class="properties-btn" href="" title="Properties"><i class="icon-settings-outline"></i></a> </div>
																</div>
																<input class="header_check" type="checkbox" id="section-components-9338">
																<label for="section-components-9338">
																	<div class="header-arrow"></div>
																</label>
																<div class="tree">
																	<ol></ol>
																</div>
															</div --> 
																											
															
													  </div>
													</div>
												</div>
											</div>
											
											<div class="tab-pane show active" id="sections-new-tab" data-section="content" role="tabpanel" aria-labelledby="content-tab">


													   <div class="search">
															  <div class="expand">
																  <button class="text-sm" title="Expand All" data-vvveb-action="expand"><i class="la la-plus"></i></button> 
																  <button title="Collapse all" data-vvveb-action="collapse"><i class="la la-minus"></i></button> 
															  </div>	

															  <input class="form-control section-search" placeholder="Search sections" type="text" data-vvveb-action="search" data-vvveb-on="keyup">
															  <button class="clear-backspace"  data-vvveb-action="clearSearch" title="Clear search">
																  <i class="la la-times"></i>
															  </button>
														</div>

													
														<div class="drag-elements-sidepane sidepane">
															  <div class="block-preview"><img src="" style="display:none"></div>
															  <div>
																  
																<ul class="sections-list clearfix" data-type="leftpanel">
																</ul>

															  </div>
														</div>

											</div>
											
										</div>
							
							  </div>
							
								<div class="tab-pane show" id="components-tabs" role="tabpanel" aria-labelledby="components-tab">
								  
								  
										<ul class="nav nav-tabs nav-fill nav-underline  sections-tabs" role="tablist">
										  <li class="nav-item components-tab">
											<a class="nav-link active" data-bs-toggle="tab" href="#components" role="tab" aria-controls="components" aria-selected="true">
												<i class="icon-cube-outline"></i> <div><span>Components</span></div></a>
										  </li>
										  <li class="nav-item blocks-tab">
											<a class="nav-link" data-bs-toggle="tab" href="#blocks" role="tab" aria-controls="components" aria-selected="false">
												<i class="icon-copy-outline"></i> <div><span>Blocks</span></div></a>
										  </li>
										</ul>
								
										<div class="tab-content">
		
											 <div class="tab-pane show active components" id="components" data-section="components" role="tabpanel" aria-labelledby="components-tab">
												 
												   <div class="search">
														  <div class="expand">
																  <button class="text-sm" title="Expand All" data-vvveb-action="expand"><i class="la la-plus"></i></button> 
																  <button title="Collapse all" data-vvveb-action="collapse"><i class="la la-minus"></i></button> 
														  </div>	

														  <input class="form-control component-search" placeholder="Search components" type="text" data-vvveb-action="search" data-vvveb-on="keyup">
														  <button class="clear-backspace" data-vvveb-action="clearSearch">
															  <i class="la la-times"></i>
															</button>
													</div>

													<div class="drag-elements-sidepane sidepane">	
														 <div>
														  
														<ul class="components-list clearfix" data-type="leftpanel">
														</ul>

													</div>											 
												</div>
											</div>

											
											
											<div class="tab-pane show active blocks" id="blocks" data-section="content" role="tabpanel" aria-labelledby="content-tab">

													   <div class="search">
															  <div class="expand">
																  <button class="text-sm" title="Expand All" data-vvveb-action="expand"><i class="la la-plus"></i></button> 
																  <button title="Collapse all" data-vvveb-action="collapse"><i class="la la-minus"></i></button> 
															  </div>	

															  <input class="form-control block-search" placeholder="Search blocks" type="text" data-vvveb-action="search" data-vvveb-on="keyup">
															  <button class="clear-backspace" data-vvveb-action="clearSearch">
																  <i class="la la-times"></i>
															  </button>
														</div>

											  
														<div class="drag-elements-sidepane sidepane">
															  <div class="block-preview"><img src=""></div>
															  <div>
																<ul class="blocks-list clearfix" data-type="leftpanel">
																</ul>

															  </div>
														</div>
											</div>
											
										</div>
							</div>

								<div class="tab-pane" id="properties" role="tabpanel" aria-labelledby="properties-tab">
									<div class="component-properties-sidepane">
										<div>
											<div class="component-properties">
												<ul class="nav nav-tabs nav-fill nav-underline" id="properties-tabs" role="tablist">
													  <li class="nav-item content-tab">
														<a class="nav-link content-tab active" data-bs-toggle="tab" href="#content-left-panel-tab" role="tab" aria-controls="components" aria-selected="true">
															<i class="icon-albums-outline"></i> <span>Content</span>
														</a>
													  </li>
													  <li class="nav-item style-tab">
														<a class="nav-link" data-bs-toggle="tab" href="#style-left-panel-tab" role="tab" aria-controls="style" aria-selected="false">
															<i class="icon-color-fill-outline"></i> <span>Style</span></a>
													  </li>
													  <li class="nav-item advanced-tab">
														<a class="nav-link" data-bs-toggle="tab" href="#advanced-left-panel-tab" role="tab" aria-controls="advanced" aria-selected="false">
															<i class="icon-settings-outline"></i> <span>Advanced</span></a>
													  </li>
													</ul>
											
													<div class="tab-content" data-offset="20">
														 <div class="tab-pane show active" id="content-left-panel-tab" data-section="content" role="tabpanel" aria-labelledby="content-tab">
															<div class="alert alert-dismissible fade show alert-light m-3" role="alert" style="">		  
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
																<strong>No selected element!</strong><br> Click on an element to edit.		
															</div>
														</div>
														
														 <div class="tab-pane show" id="style-left-panel-tab" data-section="style" role="tabpanel" aria-labelledby="style-tab">
															  <div class="border-bottom pb-2 px-2">
																<div class="justify-content-end d-flex">
																  <select class="form-select w-50" data-vvveb-action="setState" data-vvveb-on="change">
																	<option value=""> - State - </option>
																	<option value="hover">hover</option>
																	<option value="active">active</option>
																	<option value="nth-of-type(2n)">nth-of-type(2n)</option>
																  </select>
																</div>
															  </div>
														 </div>
														
														 <div class="tab-pane show" id="advanced-left-panel-tab" data-section="advanced"  role="tabpanel" aria-labelledby="advanced-tab">
														</div>
													</div>
											</div>
										</div>
									</div>
							  </div>
							
							<div class="tab-pane" id="configuration" role="tabpanel" aria-labelledby="configuration-tab">
								
									<ul class="nav nav-tabs nav-fill nav-underline sections-tabs" id="vars-tabs" role="tablist">
									  <li class="nav-item vars-tab">
										<a class="nav-link active" data-bs-toggle="tab" href="#vars-tab" role="tab" aria-controls="components" aria-selected="false">
											<i class="icon-brush-outline"></i> <div><span>Variables</span></div></a>
									  </li>
									  <li class="nav-item css-tab">
										<a class="nav-link" data-bs-toggle="tab" href="#css-tab" role="tab" aria-controls="css" aria-selected="true">
											<i class="icon-code-slash-outline"></i> <div><span>Css</span></div></a>
									  </li>
									</ul>
							
									<div class="tab-content">

										 <div class="tab-pane show active" id="vars-tab" data-section="vars" role="tabpanel" aria-labelledby="vars-tab">
								
													<div class="drag-elements-sidepane sidepane">
													<div data-offset="80">
														<div class="component-properties">
								<!-- color palette -->
															<!--
															<label class="header" data-header="default" for="header_pallette"><span>Global styles</span>
									<div class="header-arrow"></div>
															</label> -->
															<input class="header_check" type="checkbox" checked id="header_pallette">
															<div class="tab-pane section px-0" data-section="content">
										
															</div>
									
													</div>
												</div>
											</div>
										</div>
										
										 <div class="tab-pane" id="css-tab" data-section="css" role="tabpanel" aria-labelledby="css-tab">
											<div class="drag-elements-sidepane sidepane">
											<div data-offset="80">
												<textarea id="css-editor" class="form-control" rows="24"></textarea>
											</div>
											</div>
										 </div>
										
									</div>
								
							
							</div><!-- end configuration tab -->
							
							</div>
						</div>							
					
					  </div>
				</div>	
     			   </div>

<form id="editCon" method="post" action="standalone_service_html_detail_db.php">
				<div id="canvas">
					<div id="iframe-wrapper">
						<div id="iframe-layer">
							
							<div class="loading-message active">
									<div class="animation-container">
									  <div class="dot dot-1"></div>
									  <div class="dot dot-2"></div>
									  <div class="dot dot-3"></div>
									</div>

									<svg xmlns="http://www.w3.org/2000/svg" version="1.1">
									  <defs>
										<filter id="goo">
										  <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
										  <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 21 -7"/>
										</filter>
									  </defs>
									</svg>
									<!-- https://codepen.io/Izumenko/pen/MpWyXK -->
							</div>
							
							<div id="highlight-box">
								<div id="highlight-name">
								  <span class="name"></span>
								  <span class="type"></span>
								</div>
							</div>

							<div id="select-box">
								
								<div id="section-actions">
									<a id="add-section-btn" href="" title="Add element"><i class="la la-plus"></i></a>
								</div>

								<div id="wysiwyg-editor" class="default-editor">
										<!--
										<a id="bold-btn" href="" title="Bold">
											<svg height="24" viewBox="0 0 256 256" width="24" xmlns="http://www.w3.org/2000/svg">
												<path clip-rule="evenodd" d="M56 40V216H148C176.719 216 200 192.719 200 164C200 147.849 192.637 133.418 181.084 123.88C187.926 115.076 192 104.014 192 92C192 63.2812 168.719 40 140 40H56ZM88 144V184H148C159.046 184 168 175.046 168 164C168 152.954 159.046 144 148 144H88ZM88 112V72H140C151.046 72 160 80.9543 160 92C160 103.046 151.046 112 140 112H88Z" fill="#252525" fill-rule="evenodd"/>
											</svg>	
										</a>
										<a id="italic-btn" href="" title="Italic">
											<svg height="24" viewBox="0 0 256 256" width="24" xmlns="http://www.w3.org/2000/svg">
												<path d="M202 40H84V64H126.182L89.8182 192H54V216H172V192H129.818L166.182 64H202V40Z" fill="#252525"/>
											</svg>											
										</a>
										<a id="underline-btn" href="" title="Underline">
											<svg height="24" viewBox="0 0 256 256" width="24" xmlns="http://www.w3.org/2000/svg">
												<path clip-rule="evenodd" d="M88 40H60V108.004C60 145.56 90.4446 176.004 128 176.004C165.555 176.004 196 145.56 196 108.004V40H168V108C168 130.091 150.091 148 128 148C105.909 148 88 130.091 88 108V40ZM204 216V192H52V216H204Z" fill="#252525" fill-rule="evenodd"/>
											</svg>
										</a>
										--> 
										
										<a id="bold-btn" class="hint" href="" title="Bold" aria-label="Bold">
											<svg height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">
												<path d="M6,4h8a4,4,0,0,1,4,4h0a4,4,0,0,1-4,4H6Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
												<path d="M6,12h9a4,4,0,0,1,4,4h0a4,4,0,0,1-4,4H6Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
											</svg>	
										</a>
										<a id="italic-btn" class="hint" href="" title="Italic" aria-label="Italic">
											<svg height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">
												<line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="19" x2="10" y1="4" y2="4"/>
                                                <line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="14" x2="5" y1="20" y2="20"/>
												<line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="15" x2="9" y1="4" y2="20"/>
											</svg>									
										</a>
										<a id="underline-btn" class="hint" href="" title="Underline" aria-label="Underline">
											<svg height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">
												<path d="M6,4v7a6,6,0,0,0,6,6h0a6,6,0,0,0,6-6V4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" y1="2" y2="2"/>
												<line fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="4" x2="20" y1="22" y2="22"/>
											</svg>
										</a>
										
										
										<a id="strike-btn" class="hint" href="" title="Strikeout" aria-label="Strikeout">
											<del>S</del>
										</a>
										
										<div class="dropdown">
										  <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="hint" aria-label="Text align"><i class="la la-align-left"></i></span>
										  </button>

										  <div id="justify-btn" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a class="dropdown-item" href="#" data-value="Left"><i class="la la-lg la-align-left"></i> Align Left</a>
											<a class="dropdown-item" href="#" data-value="Center"><i class="la la-lg la-align-center"></i> Align Center</a>
											<a class="dropdown-item" href="#" data-value="Right"><i class="la la-lg la-align-right"></i> Align Right</a>
											<a class="dropdown-item" href="#" data-value="Full"><i class="la la-lg la-align-justify"></i> Align Justify</a>
										  </div>
										</div>
										
										<div class="separator"></div>

										<a id="link-btn" class="hint" href="" title="Create link" aria-label="Create link">
											<i class="la la-link">
										</i></a>
										
										<div class="separator"></div>
										
										<input id="fore-color" name="color" type="color" aria-label="Text color" pattern="#[a-f0-9]{6}" class="form-control form-control-color hint">
										<input id="back-color" name="background-color" type="color" aria-label="Background color" pattern="#[a-f0-9]{6}" class="form-control form-control-color hint">
										
										<div class="separator"></div>

										<select id="font-size" class="form-select" aria-label="Font size">
											<option value="">- Font size -</option>
											<option value="8px">8 px</option>
											<option value="9px">9 px</option>
											<option value="10px">10 px</option>
											<option value="11px">11 px</option>
											<option value="12px">12 px</option>
											<option value="13px">13 px</option>
											<option value="14px">14 px</option>
											<option value="15px">15 px</option>
											<option value="16px">16 px</option>
											<option value="17px">17 px</option>
											<option value="18px">18 px</option>
											<option value="19px">19 px</option>
											<option value="20px">20 px</option>
											<option value="21px">21 px</option>
											<option value="22px">22 px</option>
											<option value="23px">23 px</option>
											<option value="24px">24 px</option>
											<option value="25px">25 px</option>
											<option value="26px">26 px</option>
											<option value="27px">27 px</option>
											<option value="28px">28 px</option>
										</select>
										
										<div class="separator"></div>
										
										<select id="font-family" class="form-select" title="Font family">
											<option value=""> - Font family - </option>
											<optgroup label="System default">
												<option value="Arial, Helvetica, sans-serif">Arial</option>
												<option value="'Lucida Sans Unicode', 'Lucida Grande', sans-serif">Lucida Grande</option>
												<option value="'Palatino Linotype', 'Book Antiqua', Palatino, serif">Palatino Linotype</option>
												<option value="'Times New Roman', Times, serif">Times New Roman</option>
												<option value="Georgia, serif">Georgia, serif</option>
												<option value="Tahoma, Geneva, sans-serif">Tahoma</option>
												<option value="'Comic Sans MS', cursive, sans-serif">Comic Sans</option>
												<option value="Verdana, Geneva, sans-serif">Verdana</option>
												<option value="Impact, Charcoal, sans-serif">Impact</option>
												<option value="'Arial Black', Gadget, sans-serif">Arial Black</option>
												<option value="'Trebuchet MS', Helvetica, sans-serif">Trebuchet</option>
												<option value="'Courier New', Courier, monospace">Courier New</option>
												<option value="'Brush Script MT', sans-serif">Brush Script</option>
											</optgroup>
										</select>
								</div>



								<div id="select-actions">
								
									<!-- Trigger button (always visible) -->
									<div id="action-trigger">
										<i class="la la-ellipsis-h"></i>
									</div>
									
									<!-- Action buttons (hidden by default) -->
									<div id="action-buttons">
										<div style="display: flex; flex-wrap: wrap; gap: 6px;">
										<a id="drag-btn" href="" title="Drag element" class="d-none"><i class="la la-arrows-alt"></i></a>
										<a id="parent-btn" href="" title="Select parent"><i class="la la-level-up-alt"></i></a>
										</div>
										
										<div style="display: flex; flex-wrap: wrap; gap: 6px;">
										<a id="up-btn" href="" title="Move element up"><i class="la la-arrow-up"></i></a>
										<a id="down-btn" href="" title="Move element down"><i class="la la-arrow-down"></i></a>
										<a id="left-btn" href="" title="Move element left" style="display:none;"><i class="la la-arrow-left"></i></a>
										<a id="right-btn" href="" title="Move element right" style="display:none;"><i class="la la-arrow-right"></i></a>
										</div>
										
										<div style="display: flex; flex: 0 0 100%; flex-wrap: wrap; gap: 6px;">
										<a id="decrease-btn" href="" title="Decrease width" style="display:none;"><i class="la la-minus"></i></a>
										<a id="increase-btn" href="" title="Increase width" style="display:none;"><i class="la la-plus"></i></a>
										</div>
										
										<div style="display: flex; flex-wrap: wrap; gap: 6px;">
										<a id="edit-code-btn" href="" title="Edit html code"><i class="icon-code-outline"></i></a>
										<a id="save-reusable-btn" href="" title="Save as reusable"><i class="icon-save-outline"></i></a>
										</div>
										
										<div style="display: flex; flex-wrap: wrap; gap: 6px;">
										<a id="clone-btn" href="" title="Clone element"><i class="icon-copy-outline"></i></a>
										<a id="delete-btn" href="" title="Remove element"><i class="icon-trash-outline"></i></a>
										</div>
									</div>
								</div>
								
								<div class="resize">
									<!-- top -->
									<div class="top-left">
									</div>
									<div class="top-center">
									</div>
									<div class="top-right">
									</div>
									<!-- center -->
									<div class="center-left">
									</div>
									<div class="center-right">
									</div>
									<!-- bottom -->
									<div class="bottom-left">
									</div>
									<div class="bottom-center">
									</div>
									<div class="bottom-right">
									</div>
								</div>

							</div>
							
							<!-- add section box -->
							<div id="add-section-box" class="drag-elements">

									<div class="header">							
										<ul class="nav nav-tabs" id="box-elements-tabs" role="tablist">
										  <li class="nav-item sections-tab">
											<a class="nav-link px-3 active" data-bs-toggle="tab" href="#dimis-sections-list" role="tab" aria-controls="sections" aria-selected="true"><i class="icon-copy-outline"></i><small>Section</small></a>
										  </li>
										  <li class="nav-item component-tab">
											<a class="nav-link px-3" id="box-components-tab" data-bs-toggle="tab" href="#box-components" role="tab" aria-controls="components" aria-selected="false"><i class="icon-cube-outline"></i><small>Components</small></a>
										  </li>
										  <li class="nav-item sections-tab">
											<a class="nav-link px-3" id="box-sections-tab" data-bs-toggle="tab" href="#box-blocks" role="tab" aria-controls="blocks" aria-selected="false"><i class="icon-copy-outline"></i><small>Blocks</small></a>
										  </li>
                          <!--
										  <li class="nav-item component-properties-tab" style="display:none">
											<a class="nav-link" id="box-properties-tab" data-bs-toggle="tab" href="#box-properties" role="tab" aria-controls="properties" aria-selected="false"><i class="la la-lg la-cog"></i> <div><small>Properties</small></div></a>
										  </li>
                          -->
										</ul>
										
										<div class="section-box-actions">

									<div id="close-section-btn" class="btn btn-outline-secondary btn-sm mt-1 border-0 float-end"><i class="la la-times la-lg"></i></div>
									
										<div class="me-4 small mt-2 float-end" style="display: none;">
											
											<div class="form-check d-inline-block small me-1">
											  <input type="radio" id="add-section-insert-mode-after" value="after" checked="checked" name="add-section-insert-mode" class="form-check-input">
											  <label class="form-check-label" for="add-section-insert-mode-after">After</label>
											</div>
												
											<div class="form-check d-inline-block small">
											  <input type="radio" id="add-section-insert-mode-inside" value="inside" name="add-section-insert-mode" class="form-check-input">
											  <label class="form-check-label" for="add-section-insert-mode-inside">Inside</label>
											</div>
										
										</div>
											
										</div>
										
										<div class="tab-content">
										  <!-- dimi's sections -->
										  <div class="tab-pane show active" id="dimis-sections-list" role="tabpanel" aria-labelledby="dimis-sections-tab">
											  
											   <div class="search">
													  <div class="expand">
														  <button class="text-sm" title="Expand All" data-vvveb-action="expand"><i class="la la-plus"></i></button>
														  <button title="Collapse all" data-vvveb-action="collapse"><i class="la la-minus"></i></button> 
													  </div>	

													  <input class="form-control block-search" placeholder="Search blocks" type="text" data-vvveb-action="search" data-vvveb-on="keyup">
													  <button class="clear-backspace"  data-vvveb-action="clearSearch">
														  <i class="la la-times"></i>
													  </button>
												  </div>

												<div class="overflow-y-auto mb-5">
												  <div>
													  
													<ul class="sections-list clearfix"  data-type="addbox">
													</ul>

												  </div>
												</div>
										  
										  </div>
										  <!-- // dimi's sections -->
										  <div class="tab-pane" id="box-components" role="tabpanel" aria-labelledby="components-tab">
											  
											   <div class="search">
													  <div class="expand">
														  <button class="text-sm" title="Expand All" data-vvveb-action="expand"><i class="la la-plus"></i></button>
														  <button title="Collapse all" data-vvveb-action="collapse"><i class="la la-minus"></i></button> 
													  </div>	

													  <input class="form-control component-search" placeholder="Search components" type="text" data-vvveb-action="search" data-vvveb-on="keyup">
													  <button class="clear-backspace" data-vvveb-action="clearSearch">
														  <i class="la la-times"></i>
													  </button>
												  </div>

												<div class="overflow-y-auto mb-5">
												  <div>
													  
													<ul class="components-list clearfix" data-type="addbox">
													</ul>

												  </div>
												</div>
										  
										  </div>
										  <div class="tab-pane" id="box-blocks" role="tabpanel" aria-labelledby="blocks-tab">
											  
											   <div class="search">
													  <div class="expand">
														  <button class="text-sm" title="Expand All" data-vvveb-action="expand"><i class="la la-plus"></i></button>
														  <button title="Collapse all" data-vvveb-action="collapse"><i class="la la-minus"></i></button> 
													  </div>	

													  <input class="form-control block-search" placeholder="Search blocks" type="text" data-vvveb-action="search" data-vvveb-on="keyup">
													  <button class="clear-backspace"  data-vvveb-action="clearSearch">
														  <i class="la la-times"></i>
													  </button>
												  </div>

												<div class="overflow-y-auto mb-5">
												  <div>
													  
													<ul class="blocks-list clearfix"  data-type="addbox">
													</ul>

												  </div>
												</div>
										  
										  </div>
										
											<!-- div class="tab-pane" id="box-properties" role="tabpanel" aria-labelledby="blocks-tab">
												<div class="component-properties-sidepane">
													<div>
														<div class="component-properties">
															<div class="mt-4 text-center">Click on an element to edit.</div>
														</div>
													</div>
												</div>
											</div -->
										</div>
									</div>		

							</div>
							<!-- //add section box -->

							<div id="drop-highlight-box">
							</div>
						</div>
							
														
						<iframe src="" id="iframe1" style="width:100%; background-color: white !important; border:none;"></iframe>
					</div>
					
					
				</div>
<input type="hidden" name="serviceID" value="<?php echo $serviceID; ?>">
<input type="hidden" name="userID" value="<?php echo $page->getUserID(); ?>">
<input type="hidden" name="actionX" value="<?php echo $actionValue; ?>">
<input type="hidden" name="htmlX" value="">
</form>

				<div id="right-panel">
					<div class="component-properties">
						
						<ul class="nav nav-tabs nav-fill nav-underline" id="properties-tabs" role="tablist">
							  <li class="nav-item content-tab">
								<a class="nav-link active" data-bs-toggle="tab" href="#content-tab" role="tab" aria-controls="components" aria-selected="true">
									 <i class="icon-albums-outline"></i> <span>Content</span></a>
							  </li>
							  <li class="nav-item style-tab">
								<a class="nav-link" data-bs-toggle="tab" href="#style-tab" role="tab" aria-controls="blocks" aria-selected="false">
									<i class="icon-color-fill-outline"></i> <span>Style</span></a>
							  </li>
							  <li class="nav-item advanced-tab">
								<a class="nav-link" data-bs-toggle="tab" href="#advanced-tab" role="tab" aria-controls="blocks" aria-selected="false">
									<i class="icon-settings-outline"></i> <span>Advanced</span></a>
							  </li>
							</ul>
					
							<div class="tab-content">
								 <div class="tab-pane show active" id="content-tab" data-section="content" role="tabpanel" aria-labelledby="content-tab">
									<div class="alert alert-dismissible fade show alert-light m-3" role="alert">		  
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
										<strong>No selected element!</strong><br> Click on an element to edit.		
									</div>
								</div>
								
								 <div class="tab-pane show" id="style-tab" data-section="style" role="tabpanel" aria-labelledby="style-tab">
									<div class="border-bottom pb-2 px-2">
									  <div class="justify-content-end d-flex">
										<select class="form-select w-50" data-vvveb-action="setState" data-vvveb-on="change">
										  <option value=""> - State - </option>
										  <option value="hover">hover</option>
										  <option value="active">active</option>
										  <option value="nth-of-type(2n)">nth-of-type(2n)</option>
										</select>
									  </div>
									</div>
								</div>
								
								 <div class="tab-pane show" id="advanced-tab" data-section="advanced"  role="tabpanel" aria-labelledby="advanced-tab">
								</div>
								
								
							</div>
							
							
							
					</div>
				</div>
				
				<div id="bottom-panel">
					
						<div>

							<div class="breadcrumb-navigator px-2" style="--bs-breadcrumb-divider: '>';">
								
							  <ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">body</a></li>
								<li class="breadcrumb-item"><a href="#">section</a></li>
								<li class="breadcrumb-item"><a href="#">img</a></li>
							  </ol>			
							</div>


							<div class="btn-group" role="group">

								<div id="toggleEditorJsExecute" class="form-check mt-1" style="display:none">
									<input type="checkbox" class="form-check-input" id="runjs" name="runjs" data-vvveb-action="toggleEditorJsExecute">
									<label class="form-check-label" for="runjs"><small>Run javascript code on edit</small></label>&ensp;
								</div>
							
							
								  <button type="button" id="code-editor-btn" class="btn btn-sm btn-light btn-sm"  title="Code editor" data-vvveb-action="toggleEditor">
									  <i class="la la-code"></i> Code editor
								  </button>
								 

							</div>
							
						</div>
							
						<div id="vvveb-code-editor">
							<textarea class="form-control"></textarea>
						<div>
				</div>
				</div>
			</div>
		</div>


<!-- templates -->

<script id="vvveb-input-textinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
	</div>
	
</script>

<script id="vvveb-input-textareainput" type="text/html">
	
	<div>
		<textarea name="{%=key%}" {% if (typeof rows !== 'undefined') { %} rows="{%=rows%}" {% } else { %} rows="3" {% } %} class="form-control"/>
	</div>
	
</script>

<script id="vvveb-input-checkboxinput" type="text/html">
	
	<div class="form-check{% if (typeof className !== 'undefined') { %} {%=className%}{% } %}">
		  <input name="{%=key%}" class="form-check-input" type="checkbox" id="{%=key%}_check">
		  <label class="form-check-label" for="{%=key%}_check">{% if (typeof text !== 'undefined') { %} {%=text%} {% } %}</label>
	</div>
	
</script>

<script id="vvveb-input-radioinput" type="text/html">
	
	<div>
	
		{% for ( var i = 0; i < options.length; i++ ) { %}

		<label class="form-check-input  {% if (typeof inline !== 'undefined' && inline == true) { %}custom-control-inline{% } %}"  title="{%=options[i].title%}">
		  <input name="{%=key%}" class="form-check-input" type="radio" value="{%=options[i].value%}" id="{%=key%}{%=i%}" {%if (options[i].checked) { %}checked="{%=options[i].checked%}"{% } %}>
		  <label class="form-check-label" for="{%=key%}{%=i%}">{%=options[i].text%}</label>
		</label>

		{% } %}

	</div>
	
</script>

<script id="vvveb-input-radiobuttoninput" type="text/html">
	
	<div class="btn-group btn-group-sm {%if (extraclass) { %}{%=extraclass%}{% } %} clearfix" role="group">
		{% var namespace = 'rb-' + Math.floor(Math.random() * 100); %}
		
		{% for ( var i = 0; i < options.length; i++ ) { %}

		<input name="{%=key%}" class="btn-check" type="radio" value="{%=options[i].value%}" id="{%=namespace%}{%=key%}{%=i%}" {%if (options[i].checked) { %}checked="{%=options[i].checked%}"{% } %} autocomplete="off">
		<label class="btn btn-outline-primary {%if (options[i].extraclass) { %}{%=options[i].extraclass%}{% } %}" for="{%=namespace%}{%=key%}{%=i%}" title="{%=options[i].title%}">
		  {%if (options[i].icon) { %}<i class="{%=options[i].icon%}"></i>{% } %}
		  {%=options[i].text%}
		</label>

		{% } %}
				
	</div>
	
</script>


<script id="vvveb-input-toggle" type="text/html">
	
    <div class="form-check form-switch {% if (typeof className !== 'undefined') { %} {%=className%}{% } %}">
        <input 
		type="checkbox" 
		name="{%=key%}" 
		value="{%=on%}" 
		{%if (off !== null) { %} data-value-off="{%=off%}" {% } %}
		{%if (on !== null) { %} data-value-on="{%=on%}" {% } %} 
		class="form-check-input" type="checkbox" role="switch"
		id="{%=key%}">
        <label class="form-check-label"for="{%=key%}">
        </label>
    </div>
	
</script>

<script id="vvveb-input-header" type="text/html">

		<h6 class="header">{%=header%}</h6>
	
</script>

	
<script id="vvveb-input-select" type="text/html">

	<div>

		<select class="form-select" name="{%=key%}">
			{% var optgroup = false; for ( var i = 0; i < options.length; i++ ) { %}
				{% if (options[i].optgroup) {  %}
					{% if (optgroup) {  %}
						</optgroup>
					{% } %}
					<optgroup label="{%=options[i].optgroup%}">
				{% optgroup = true; } else { %}
			<option value="{%=options[i].value%}" 
				{% 
					for (attr in options[i]) {
							if (attr != "value" && attr != "text") {
						 %} 
							{%=attr%}={%=options[i][attr]%} 
						{% } 
					} %}>
			{%=options[i].text%}</option>
			{% } } %}
		</select>
	
	</div>
	
</script>

<script id="vvveb-input-icon-select" type="text/html">

	<div class="input-list-select">
		
		<div class="elements">
			<div class="row">
				{% for ( var i = 0; i < options.length; i++ ) { %}
				<div class="col">
					<div class="element">
						{%=options[i].value%}
						<label>{%=options[i].text%}</label>
					</div>
				</div>
				{% } %}
			</div>
		</div>
	</div>
	
</script>

<script id="vvveb-input-html-list-select" type="text/html">

	<div class="input-html-list-select">
		
		<div class="current-element">
		</div>
		
		<div class="popup">
			<select class="form-select">
				{% var optgroup = false; for ( var i = 0; i < options.length; i++ ) { %}
					{% if (options[i].optgroup) {  %}
						{% if (optgroup) {  %}
							</optgroup>
						{% } %}
						<optgroup label="{%=options[i].optgroup%}">
					{% optgroup = true; } else { %}
					<option value="{%=options[i].value%}">{%=options[i].text%}</option>
				{% } } %}
			</select>
			
		      <div class="search">
				  <input class="form-control search" placeholder="Search elements" type="text">
				  <button class="clear-backspace">
					  <i class="la la-times"></i>
				  </button>
			</div>
			
			<div class="elements">
					{%=elements%}
			</div>
		</div>
	</div>
	
</script>

<script id="vvveb-input-html-list-dropdown" type="text/html">

	<div class="input-html-list-select" {% if (typeof id !== "undefined") { %} id={%=id%} {% } %}>
		
		<div class="current-element">
		
		</div>
		
		<div class="popup">
			<select class="form-select">
				{% var optgroup = false; for ( var i = 0; i < options.length; i++ ) { %}
					{% if (options[i].optgroup) {  %}
						{% if (optgroup) {  %}
							</optgroup>
						{% } %}
						<optgroup label="{%=options[i].optgroup%}">
					{% optgroup = true; } else { %}
					<option value="{%=options[i].value%}">{%=options[i].text%}</option>
				{% } } %}
			</select>
			
		      <div class="search">
				  <input class="form-control search" placeholder="Search elements" type="text">
				  <button class="clear-backspace">
					  <i class="la la-times"></i>
				  </button>
			</div>
			
			<div class="elements">
					{%=elements%}
			</div>
		</div>
	</div>
	
</script>

<script id="vvveb-input-dateinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="date" class="form-control" 
			{% if (typeof min_date === 'undefined') { %} min="{%=min_date%}" {% } %} {% if (typeof max_date === 'undefined') { %} max="{%=max_date%}" {% } %}
		/>
	</div>
	
</script>

<script id="vvveb-input-listinput" type="text/html">

	<div class="sections-container">

		{% for ( var i = 0; i < options.length; i++ ) { %}
		<div class="section-item" draggable="true">
			<div class="controls">
				<div class="handle"></div>
				<div class="info">
					<div class="name">{%=options[i].name%}
						<div class="type">{%=options[i].type%}</div>
				</div>
			  </div>
				<div class="buttons">
					<a class="delete-btn" href="" title="Remove section"><i class="icon-trash-outline text-danger"></i></a>
					<!-- 
					<a class="up-btn" href="" title="Move element up"><i class="la la-arrow-up"></i></a>
					<a class="down-btn" href="" title="Move element down"><i class="la la-arrow-down"></i></a>
					<a class="properties-btn" href="" title="Properties"><i class="icon-settings-outline"></i></a>
					-->
		</div>
			</div>


			<input class="header_check" type="checkbox" id="section-components-{%=options[i].suffix%}">

			<label for="section-components-{%=options[i].suffix%}"> 
				<div class="header-arrow"></div>
			</label>
			
			<div class="tree">
				{%=options[i].name%}
			</div>
		</div>
		



		{% } %}


		{% if (typeof hide_remove === 'undefined') { %}
		<div class="mt-3">
		
			<button class="btn btn-sm btn-outline-primary btn-new">
				<i class="la la-plus la-lg"></i> Add new
			</button>
			
		</div>
		{% } %}
			
	</div>
	
</script>

<script id="vvveb-input-grid" type="text/html">

	<div class="row">
		<div class="col-6">
		
			<label>Extra small</label>
			<select class="form-select" name="col" autocomplete="off">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col !== 'undefined') && col == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		
		<div class="col-6">
			<label>Small</label>
			<select class="form-select" name="col-sm" autocomplete="off">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_sm !== 'undefined') && col_sm == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		<div class="col-6">
			<label>Medium</label>
			<select class="form-select" name="col-md" autocomplete="off">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_md !== 'undefined') && col_md == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		<div class="col-6">
			<label>Large</label>
			<select class="form-select" name="col-lg" autocomplete="off">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_lg !== 'undefined') && col_lg == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		
		<div class="col-6">
			<label>Extra large </label>
			<select class="form-select" name="col-xl" autocomplete="off">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_xl !== 'undefined') && col_xl == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		<div class="col-6">
			<label>Extra extra large</label>
			<select class="form-select" name="col-xxl" autocomplete="off">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_xxl !== 'undefined') && col_xxl == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		{% if (typeof hide_remove === 'undefined') { %}
		<div class="col-12">
		
			<button class="btn btn-sm btn-outline-light text-danger">
				<i class="la la-trash la-lg"></i> Remove
			</button>
			
		</div>
		{% } %}
		
	</div>
	
</script>

<script id="vvveb-input-textvalue" type="text/html">
	
	<div class="row">
		<div class="col-6 mb-1">
			<label>Value</label>
			<input name="value" type="text" value="{%=value%}" class="form-control" autocomplete="off" />
		</div>

		<div class="col-6 mb-1">
			<label>Text</label>
			<input name="text" type="text" value="{%=text%}" class="form-control" autocomplete="off" />
		</div>

		{% if (typeof hide_remove === 'undefined') { %}
		<div class="col-12">
		
			<button class="btn btn-sm btn-outline-light text-danger">
				<i class="la la-trash la-lg"></i> Remove
			</button>
			
		</div>
		{% } %}

	</div>
	
</script>

<script id="vvveb-input-rangeinput" type="text/html">
	
	<div class="input-range">
		
		<input name="{%=key%}" type="range" min="{%=min%}" max="{%=max%}" step="{%=step%}" class="form-range" data-input-value/>
		<input name="{%=key%}" type="number" min="{%=min%}" max="{%=max%}" step="{%=step%}" class="form-control" data-input-value/>
	</div>
	
</script>

<script id="vvveb-input-imageinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
		<input name="file" type="file" class="form-control"/>
	</div>
	
</script>

<script id="vvveb-input-imageinput-gallery" type="text/html">
	
	<div>
		<img id="thumb-{%=key%}" class="img-thumbnail p-0" data-target-input="#input-{%=key%}" data-target-thumb="#thumb-{%=key%}" style="cursor:pointer" src="" width="225" height="225">
		<input name="{%=key%}" type="text" class="form-control mt-1" id="input-{%=key%}"/>
		<button name="button" class="btn btn-primary btn-sm btn-icon mt-2 px-2" data-target-input="#input-{%=key%}" data-target-thumb="#thumb-{%=key%}"><i class="la la-image la-lg"></i><span>Set image</span></button>
	</div>
	
</script>

<script id="vvveb-input-videoinput-gallery" type="text/html">
	
	<div>
		<video id="thumb-v{%=key%}" class="img-thumbnail p-0" data-target-input="#input-v{%=key%}" data-target-thumb="#thumb-v{%=key%}" style="cursor:pointer" src="" width="225" height="225" playsinline loop muted controls></video>
		<input name="v{%=key%}" type="text" class="form-control mt-1" id="input-v{%=key%}"/>
		<button name="button" class="btn btn-primary btn-sm btn-icon mt-2 px-2" data-target-input="#vinput-v{%=key%}" data-target-thumb="#thumb-v{%=key%}"><i class="la la-video la-lg"></i><span>Set video</span></button>
	</div>
	
</script>

<script id="vvveb-input-colorinput" type="text/html">
	
	<div>
		<input name="{%=key%}" {%  if (typeof palette !== 'undefined') { %} list="{%=key%}-color-palette" {% } %} type="color" {% if (typeof value !== 'undefined' && value != false) { %} value="{%=value%}" {% } %}  pattern="#[a-f0-9]{6}" class="form-control form-control-color"/>
		{%  if (typeof palette !== 'undefined') { %}
		<datalist id="{%=key%}-color-palette">
			{% for (const color in palette) { %}
				<option>{%=palette[color]%}</option>
			{% } %}		
		{% } %}
	</div>
	
</script>

<script id="vvveb-input-bootstrap-color-picker-input" type="text/html">
	
	<div>
		<div id="cp2" class="input-group" title="Using input value">
		  <input name="{%=key%}" type="text" {% if (typeof value !== 'undefined' && value != false) { %} value="{%=value%}" {% } %}	 class="form-control"/>
		  <span class="input-group-append">
			<span class="input-group-text colorpicker-input-addon"><i></i></span>
		  </span>
		</div>
	</div>

</script>

<script id="vvveb-input-numberinput" type="text/html">
	<div>
		<input name="{%=key%}" type="number" value="{%=value%}" 
			  {% if (typeof min !== 'undefined' && min != false) { %}min="{%=min%}"{% } %} 
			  {% if (typeof max !== 'undefined' && max != false) { %}max="{%=max%}"{% } %} 
			  {% if (typeof step !== 'undefined' && step != false) { %}step="{%=step%}"{% } %} 
		class="form-control"/>
	</div>
</script>

<script id="vvveb-input-button" type="text/html">
	<div>
		<button class="btn btn-sm btn-primary">
			<i class="la  {% if (typeof icon !== 'undefined') { %} {%=icon%} {% } else { %} la-plus {% } %} la-lg"></i> {%=text%}
		</button>
	</div>		
</script>

<script id="vvveb-input-cssunitinput" type="text/html">
	<div class="input-group css-unit" id="cssunit-{%=key%}">
		<input name="number" type="number"  {% if (typeof value !== 'undefined' && value != false) { %} value="{%=value%}" {% } %} 
			  {% if (typeof min !== 'undefined' && min != false) { %}min="{%=min%}"{% } %} 
			  {% if (typeof max !== 'undefined' && max != false) { %}max="{%=max%}"{% } %} 
			  {% if (typeof step !== 'undefined' && step != false) { %}step="{%=step%}"{% } %} 
		class="form-control"/>
		<select class="form-select small-arrow" name="unit">
			<option value="em">em</option>
			<option value="rem">rem</option>
			<option value="px">px</option>
			<option value="%">%</option>
			<option value="vw">vw</option>
			<option value="vh">vh</option>
			<option value="ex">ex</option>
			<option value="ch">ch</option>
			<option value="cm">cm</option>
			<option value="mm">mm</option>
			<option value="in">in</option>
			<option value="pt">pt</option>
			<option value="auto">auto</option>
			<option value="">-</option>
		</select>
	</div>
	
</script>


<script id="vvveb-filemanager-folder" type="text/html">
	<li data-folder="{%=folder%}" class="folder">
		<label for="{%=folder%}"><span>{%=folderTitle%}</span></label> <input type="checkbox" id="{%=folder%}" />
		<ol></ol>
	</li>
</script>

<script id="vvveb-filemanager-page" type="text/html">
	<li data-url="{%=url%}" data-file="{%=file%}" data-page="{%=name%}" class="file{% if (typeof className !== 'undefined') { %} {%=className%}{% } %}">
		<label for="{%=name%}" {% if (typeof description !== 'undefined') { %} title="{%=description%}" {% } %}>
			<span>{%=title%}</span>
			<div class="file-actions">
				<button href="#" class="delete btn btn-outline-danger" title="Delete"><i class="la la-trash"></i></button>
				<a href="{%=url%}" target="_blank" class="view btn btn-outline-primary" title="View page"><i class="la la la-external-link-alt"></i></a>
				<button href="#" class="rename btn btn-outline-primary" title="Rename"><i class="la la-pen"></i></button>
			</div>
		</label> <input type="checkbox" id="{%=name%}" />
		<!-- <ol></ol> -->
	</li>
</script>

<script id="vvveb-filemanager-component" type="text/html">
	<li data-url="{%=url%}" data-component="{%=name%}" class="component">
		<a href="{%=url%}"><span>{%=title%}</span></a>
	</li>
</script>

<script id="vvveb-breadcrumb-navigaton-item" type="text/html">
	<li class="breadcrumb-item"><a href="#" {% if (typeof className !== 'undefined') { %}class="{%=className%}"{% } %}>{%=name%}</a></li>
</script>

<script id="vvveb-input-sectioninput" type="text/html">
	<div>
		{% var namespace = '-' + Math.floor(Math.random() * 1000); %}
		<label class="header" data-header="{%=key%}" for="header_{%=key%}{%=namespace%}" {% if (typeof group !== 'undefined' && group != null) { %}data-group="{%=group%}" {% } %}>
			<span>{%=header%}</span> 
			<div class="header-arrow"></div>
		</label> 
		<input class="header_check" type="checkbox" {% if (typeof expanded !== 'undefined' && expanded == false) { %} {% } else { %}checked="true"{% } %} id="header_{%=key%}{%=namespace%}"> 
		<div class="section row" data-section="{%=key%}" {% if (typeof group !== 'undefined' && group != null) { %}data-group="{%=group%}" {% } %}></div>		
	</div>
</script>


<script id="vvveb-property" type="text/html">

	<div class="mb-3 {% if (typeof col !== 'undefined' && col != false) { %} col-sm-{%=col%} {% } else { %}row{% } %} {% if (typeof inline !== 'undefined' && inline == true) { %}inline{% } %} " data-key="{%=key%}" {% if (typeof group !== 'undefined' && group != null) { %}data-group="{%=group%}" {% } %}>
		
		{% if (typeof name !== 'undefined' && name != false) { %}<label class="{% if (typeof inline === 'undefined' ) { %}col-sm-4{% } %} form-label" for="input-model">{%=name%}</label>{% } %}
		
		<div class="{% if (typeof inline === 'undefined') { %}col-sm-{% if (typeof name !== 'undefined' && name != false) { %}8{% } else { %}12{% } } %} input"></div>
		
	</div>		 
	
</script>

<script id="vvveb-input-autocompletelist" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
		
		<div class="form-control autocomplete-list" style="min-height: 150px; overflow: auto;">
                  </div>
                  </div>
	
</script>

<script id="vvveb-input-tagsinput" type="text/html">
	
	<div>
		<div class="form-control tags-input" style="height:auto;">
				

				<input name="{%=key%}" type="text" class="form-control" style="border:none;min-width:60px;"/>
                  </div>
                  </div>
	
</script>

<script id="vvveb-input-noticeinput" type="text/html">
	<div>
		<div class="alert alert-dismissible fade show alert-{%=type%}" role="alert">		  
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
			<strong class="d-block mb-1">{%=title%}</strong> 
			
			{%=text%}
		</div>
	</div>
</script>

<script id="vvveb-section" type="text/html">
	{% var suffix = Math.floor(Math.random() * 10000); %}

	<div class="section-item" draggable="true">
		<div class="controls">
			<div class="handle"></div>
			<div class="info">
				<div class="name">{%=name%} 
					<div class="type">{%=type%}</div>
				</div>
			</div>
			<div class="buttons">
				<a class="delete-btn" href="" title="Remove section"><i class="la la-trash text-danger"></i></a>
				<!-- 
				<a class="up-btn" href="" title="Move element up"><i class="la la-arrow-up"></i></a>
				<a class="down-btn" href="" title="Move element down"><i class="la la-arrow-down"></i></a>
				-->
				<a class="properties-btn" href="" title="Properties"><i class="icon-settings-outline"></i></a>
		</div>
		</div>


		<input class="header_check" type="checkbox" id="section-components-{%=suffix%}">

		<label for="section-components-{%=suffix%}"> 
			<div class="header-arrow"></div>
		</label>
		
		<div class="tree">
			<ol>
				<!--
				<li data-component="Products" class="file">							
					<label for="idNaN" style="background-image:url(/js/vvvebjs/icons/products.svg)"><span>Products</span></label>							
					<input type="checkbox" id="idNaN">
				</li>
				<li data-component="Posts" class="file">							
					<label for="idNaN" style="background-image:url(/js/vvvebjs/icons/posts.svg)"><span>Posts</span></label>							
					<input type="checkbox" id="idNaN">
				</li>
				-->
			</ol>
		</div>
	</div>
	
</script>


<!--// end templates -->


        <div id="tree-list">
          <div class="header">
            <div>Navigator</div>
            <button class="btn btn-sm" data-vvveb-action="toggleTreeList" aria-pressed="true">
              <i class="icon-close"></i>
            </button>
          </div>
          <div class="tree">
            <ol>
              <!--
			<li data-component="Products" class="file">							
				<label for="idNaN" style="background-image:url(/js/vvvebjs/icons/products.svg)"><span>Products</span></label>							
				<input type="checkbox" id="idNaN">
			</li>
			<li data-component="Posts" class="file">							
				<label for="idNaN" style="background-image:url(/js/vvvebjs/icons/posts.svg)"><span>Posts</span></label>							
				<input type="checkbox" id="idNaN">
			</li>
			-->
            </ol>
          </div>
        </div>

<!-- code editor modal -->
<div class="modal modal-full fade" id="codeEditorModal" tabindex="-1" aria-labelledby="codeEditorModal" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
	  <input type="hidden" name="file" value="">  	
	
      <div class="modal-header justify-content-between">
        <span class="modal-title"></span>
		<div class="float-end">
			<button type="button" class="btn btn-light border btn-icon" data-bs-dismiss="modal"><i class="la la-times"></i>Close</button>
			
			<button class="btn btn-primary btn-icon save-btn" title="Save changes">
				<span class="loading d-none">
				<i class="la la-save"></i>
				  <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true">
				  </span>
				  <span>Saving </span> ... </span>

				<span class="button-text">
				  <i class="la la-save"></i> <span>Save changes</span>
				</span>				
			</button>
		</div>
      </div>
	  
      <div class="modal-body p-0">
        <textarea class="form-control h-100"></textarea>
      </div>
	  

    </div>
  </div>
</div>

<!-- export html modal-->
<div class="modal fade" id="textarea-modal" tabindex="-1" role="dialog" aria-labelledby="textarea-modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title text-primary"><i class="la la-lg la-save"></i> Export html</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <!-- span aria-hidden="true"><small><i class="la la-times"></i></small></span -->
        </button>
      </div>
      <div class="modal-body">
        
        <textarea rows="25" cols="150" class="form-control"></textarea>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal"><i class="la la-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- message modal-->
<div class="modal fade" id="message-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title text-primary"><i class="la la-lg la-comment"></i> VvvebJs</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <!-- span aria-hidden="true"><small><i class="la la-times"></i></small></span -->
        </button>
      </div>
      <div class="modal-body">
        <p>Page was successfully saved!.</p>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-primary">Ok</button> -->
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal"><i class="la la-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- new page modal-->
<div class="modal fade" id="new-page-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    
    <form action="save.php">
		
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title text-primary fw-normal"><i class="la la-lg la-file"></i> New page</span>
        <button type="button" class="btn-close p-0 me-1" data-bs-dismiss="modal" aria-label="Close">
          <!-- span aria-hidden="true"><small><i class="la la-times"></i></small></span -->
        </button>
      </div>

      <div class="modal-body text">
		<div class="mb-3 row" data-key="type">      
			<label class="col-sm-3 col-form-label">
				Template 
				<abbr title="The contents of this template will be used as a start for the new template">
                    <i class="la la-lg la-question-circle text-primary"></i>
				</abbr>
				
			</label>      
			<div class="col-sm-9 input">
				<div>    
					<select class="form-select" name="startTemplateUrl">        
						<option value="new-page-blank-template.html">Blank Template</option>        
						<option value="demo/narrow-jumbotron/index.html">Narrow jumbotron</option>       
						<option value="demo/album/index.html">Album</option>       
					</select>    
				</div>
			</div>     
		</div>

		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 col-form-label">Page name</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="title" type="text" value="My page" class="form-control" placeholder="My page" required>  
				</div>
			</div>     
		</div>
		
		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 col-form-label">File name</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="file" type="text" value="my-page.html" class="form-control" placeholder="my-page.html" required>  
				</div>
			</div>     
		</div>
		
		<!-- 
		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 form-label">Url</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="url" type="text" value="my-page.html" class="form-control" placeholder="/my-page.html" required>  
				</div>
			</div>     
		</div>
		-->
		
		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 col-form-label">Save to folder</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="folder" type="text" value="my-pages" class="form-control" placeholder="/" required>  
				</div>
			</div>     
		</div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary btn-icon" type="reset" data-bs-dismiss="modal"><i class="la la-times"></i> Cancel</button>
		<button class="btn btn-primary btn-icon" type="submit"><i class="la la-check"></i> Create page</button>
      </div>
    </div>
    
   </form>		

  </div>
</div>

<!-- save toast -->
<div class="toast-container position-fixed end-0 bottom-0 me-3 mb-3" id="top-toast">
  <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
	    <div class="toast-header text-white">
			<strong class="me-auto">Page save</strong>
			<!-- <small class="badge bg-success">status</small> -->
			<button type="button" class="btn-close text-white px-2" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
		<div class="toast-body ">
			<div class="flex-grow-1">
			  <div class="message">Elements saved!
				  <div>Template backup was saved!</div>
				  <div>Template was saved!</div>
			  </div>
			  <!--
			  <div><a class="btn btn-success  btn-icon btn-sm w-100 mt-2" href="">View page</a></div>
			  -->
			</div>
		</div>
	</div>
</div>

<!-- jquery-->	
<?php echo "<script type='text/javascript' src='".ADMIN_TEMPLATE_LIMITLESS_JS."main/jquery.min.js'></script>\n"; ?>

<!-- bootstrap-->
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- builder code-->
<script src="libs/builder/builder.js"></script>	
<script src="libs/builder/builder-dimi.js"></script>	
<!-- undo manager-->
<script src="libs/builder/undo.js"></script>	
<!-- inputs-->
<script src="libs/builder/inputs.js"></script>	


<!-- media gallery -->
<link href="libs/media/media.css" rel="stylesheet">
<script>
window.mediaPath = '../../media';
Vvveb.themeBaseUrl = 'startup/';
</script>
<script src="libs/media/media.js"></script>	
<!--
<script src="libs/media/openverse.js"></script>
-->
<script src="libs/builder/plugin-media.js"></script>	

<!-- bootstrap colorpicker //uncomment bellow scripts to enable -->
<!--
<script src="libs/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<link href="libs/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<script src="libs/builder/plugin-bootstrap-colorpicker.js"></script>
-->

<!-- components-->
<!-- script src="libs/builder/components-server.js"></script -->	
<script src="libs/builder/plugin-google-fonts.js"></script>	
<script src="libs/builder/components-common.js"></script>	
<script src="libs/builder/plugin-aos.js"></script>
<script src="libs/builder/components-html.js"></script>	
<script src="libs/builder/components-elements.js"></script>	
<script src="libs/builder/section.js"></script>	
<script src="libs/builder/components-bootstrap5.js"></script>	
<script src="libs/builder/components-widgets.js"></script>	
<script src="libs/builder/oembed.js"></script>
<script src="libs/builder/components-embeds.js"></script>


<!-- sections-->
<script src="startup/sections.js"></script>
<script src="libs/builder/sections-bootstrap4.js"></script>

<!-- blocks-->
<script src="libs/builder/blocks-bootstrap4.js"></script>

<!-- plugins -->

<!-- code mirror - code editor syntax highlight -->
<link href="libs/codemirror/lib/codemirror.css" rel="stylesheet"/>
<link href="libs/codemirror/theme/material.css" rel="stylesheet"/>
<script src="libs/codemirror/lib/codemirror.js"></script>
<script src="libs/codemirror/lib/xml.js"></script>
<script src="libs/codemirror/lib/css.js"></script>
<script src="libs/codemirror/lib/formatting.js"></script>
<script src="libs/builder/plugin-codemirror.js"></script>	

<script>
let renameUrl = 'save.php?action=rename';
let deleteUrl = 'save.php?action=delete';	
let saveReusableUrl = 'save.php?action=saveReusable';	
let oEmbedProxyUrl = 'save.php?action=oembedProxy';
let chatgptOptions = {"key":"","model":"gpt-3.5-turbo-instruct","temperature":0,"max_tokens":300};


<?php if ($hasContent): ?>

    // Has content - init normally
    Vvveb.Builder.init('_temp/temp_<?=$serviceID?>.html', function() {
        Vvveb.TreeList.loadComponents();
    });
    setTimeout(function() {
        $.post('delete_temp.php', {serviceID: <?=$serviceID?>});
    }, 2000);
	
<?php else: ?>

    // Empty state - init builder but with modal shown. 
	// Editor specific behaviour -> see at bottom of doc ready
    Vvveb.Builder.init('about:blank', function() {
        Vvveb.TreeList.loadComponents();
    });
	
<?php endif; ?>
	
Vvveb.Gui.init();
Vvveb.FileManager.init();
Vvveb.SectionList.init();
Vvveb.TreeList.init();
Vvveb.Breadcrumb.init();
Vvveb.CssEditor.init();

Vvveb.Gui.toggleLeftColumn(false);
Vvveb.Gui.toggleRightColumn(false);
Vvveb.Gui.toggleFileManager(false);
Vvveb.Gui.toggleTreeList(false);
Vvveb.Breadcrumb.init();

// Tree navigator: Load components when user toggles it visible
// Needs setTimeout because the panel must be displayed (CSS transition complete)
// before TreeList can properly render the DOM tree structure into it
document.querySelector('#toggle-tree-list').addEventListener('click', function() {
    setTimeout(() => Vvveb.TreeList.loadComponents(), 100);
});

</script>


<!-- DIMI SPECIFIC SCRIPTS -->
<script type="text/javascript" src="saveimages.js"></script>

<script>
$(function() {
	
	$('#btnSave').on('click', function () {
		const htmlContent = Vvveb.Builder.getHtmlJIM();
		
		if (!htmlContent || !htmlContent.trim()) {
			alert('You have not added any content yet!');
			return;
		}
		
		$('#btnSave').prop('disabled', true);
		
		// Save images first, then save HTML to DB
		$(window.FrameDocument.body).saveimages({
			handler: 'saveimage.php',
			onComplete: function () {
				$.ajax({
					type: 'POST',
					url: $('#editCon').prop('action'),
					data: {
						htmlX: htmlContent,
						serviceID: $('input[name=serviceID]').val(),
						userID: $('input[name=userID]').val(),
						actionX: $('input[name=actionX]').val()
					},
					success: function(data) {
						$('body').append(data);
						window.location.reload();
					},
					error: function() {
						alert('Save failed');
						$('#btnSave').prop('disabled', false);
					}
				});
			}
		});
		$(window.FrameDocument.body).data('saveimages').save();
	});
	
	
    /*****************************************************
	  Editor specific behaviour whether builder 
	  has content or not  IN DOC READY
	*****************************************************/

	<?php if (!$hasContent): ?>
		// Only run empty state check when we KNOW it's empty
		if (window.FrameDocument.body.children.length === 0) {
			insertDummyButton();
		}
	<?php endif; ?>

	function insertDummyButton() {
		if (window.FrameDocument && window.FrameDocument.body) {
			if (window.FrameDocument.body.children.length === 0) {
							
				const btn = window.FrameDocument.createElement('button');
				btn.className = 'empty-state-btn';
				btn.textContent = 'Oh, nothing here. + Click me to add your first content +';
				
				// Style it as a big block, like in Contenbuilder.js
				btn.style.cssText = "display: block; width: 70%; height: 200px; margin: 0 auto; font-family: sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; justify-content: center; align-items: center; color: #333; border: 1px dashed rgba(169, 169, 169, 0.8); background: rgba(255, 255, 255, 0.5); cursor: pointer; transition: all ease 0.3s; outline: none !important;";
				
				// on click, we also need to hide VvvbJS default '+' button end action options
				// this is done with pure CSS, when body has class 'no-content'. 
				// see the style declarations at <head>
				btn.onclick = function(e) {
					e.stopPropagation();
					// Set body to be now the selected element, since button is gone and canvas empty
					Vvveb.Builder.selectNode(window.FrameDocument.body);
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
							Vvveb.Builder.selectNode(window.FrameDocument.body.lastElementChild);
						}, 50);
						
					}, { once: true });					
				};
				
				window.FrameDocument.body.appendChild(btn);
			}
		}
	}

	// Watch for deletions in the iframe body
	// When all elements are deleted (body empty), revert to empty state.
	// We use my CustomEvent('vvveb.component.removed')) -> builder.js aprox. line 1848
	// - Show no-content class in body
	// - Select body as insertion target
	// - Set insert mode to 'inside'
	document.addEventListener('vvveb.component.removed', function() {
		setTimeout(function() {
			if (window.FrameDocument.body.children.length === 0) {
				document.body.classList.add('no-content');
				
				Vvveb.Builder.selectNode(window.FrameDocument.body);
				
				insertDummyButton();
			}
		}, 50);
	});

}); //doc ready 
</script>
</body>
</html>
