<?php
	use WebFX\System;
	
	use WebFX\WebApplicationTask;
	
	System::$Tasks = array
	(
		// new WebApplicationTask($id, $title = null, $taskType = null, $description = null, $targetURL = null, $targetScript = null, $targetFrame = null, $tasks = null)
		new WebApplicationTask("tskAccountLogOff", "Log Off", null, "Saves your settings and closes your authenticated session", "~/account/logout.page", null, null, null),
		new WebApplicationTask("tskModuleTenant", "Create User", null, "Creates a new user", "~/users/modify", null, null, null),
		new WebApplicationTask("tskDataTypeCreate", "Create Data Type", null, "Creates a new data type", "~/data-types/modify", null, null, null),
		new WebApplicationTask("tskModuleCreate", "Create Module", null, "Creates a new global module", "~/modules/modify", null, null, null),
		new WebApplicationTask("tskAccountStartProxy", "Start Proxy", null, "Starts a proxy session authenticated automatically by another user's credentials", "~/account/proxyStart.page", null, null, null),
		new WebApplicationTask("tskAccountStopProxy", "Stop Proxy", null, "Ends the current proxy session (if a proxy session is in progress) and switches to the original user's credentials", "~/account/proxyStop.page", null, null, null)
	);
?>