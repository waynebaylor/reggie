

<script type="text/javascript">
	dojo.require("hhreg.xhrEditForm");
	dojo.require("dijit.MenuBar");
	dojo.require("dijit.MenuBarItem");
	
	dojo.addOnLoad(function() {
		document.getElementsByName("email")[0].focus();

		var menuBar = new dijit.MenuBar({}, dojo.byId("action-menu-bar"));
		menuBar.addChild(new dijit.MenuBarItem({disabled: true, label: "&nbsp;", onClick: function() {}}));
		menuBar.startup();
	});				
</script>

<div id="header">
	<?php echo $this->HTML->img(array(
		'src' => '/images/cm_logo.jpg',
		'style' => 'vertical-align: top; border: 1px solid #fff;',
		'alt' => 'Conference Managers Logo'
	)) ?>
	Registration System
</div>	

<div id="action-menu-bar"></div>

<div id="content">
	<div class="fragment-edit">
		<h3>Please Log In</h3>
		
		<?php echo $this->xhrTableForm(array(
			'url' => '/admin/Login', 
			'action' => 'login', 
			'rows' => $this->getFormRows(),
			'buttonText' => 'Submit',
			'errorText' => 'There was a problem. Please try again.',
			'redirectUrl' => '/admin/Login'
		)) ?>
	</div>
</div>
