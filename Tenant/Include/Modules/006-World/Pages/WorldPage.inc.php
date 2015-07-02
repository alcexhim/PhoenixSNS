<?php
	namespace PhoenixSNS\Modules\World\Pages
	{
		use WebFX\System;
		use WebFX\WebScript;
		
		use WebFX\Controls\ProgressBar;
		use WebFX\Controls\Window;

		use PhoenixSNS\Modules\World\Objects\Place;
		use PhoenixSNS\Controls\PhoenixVision;
		
		use PhoenixSNS\MasterPages\WebPage;
		
		class WorldPage extends WebPage
		{
			public $CurrentPlace;
			
			protected function Initialize()
			{
				parent::Initialize();
				
				$this->Scripts[] = new WebScript("~/Scripts/World.js");		// World
			}
			protected function RenderContent()
			{
?>
<input type="text" id="txtChat" placeholder="Type to say something..." style="width: 100%; display: block;" />
<script type="text/javascript">
	var txtChat = document.getElementById("txtChat");
	txtChat.addEventListener("keyup", function(e)
	{
		if (e.keyCode == 13)
		{
			var text = txtChat.value;
			
			// send
			chat_sendChat(text);
			txtChat.value = "";
		}
	});
</script>
<?php
				$pv = new PhoenixVision("world");
				$pv->CurrentPlace = $this->CurrentPlace;
				$pv->Render();
			}
		}
	}
?>