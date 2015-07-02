<?php
	if (count($path) > 1 && $path[1] != "")
	{
		$id = $path[1];
		$chance = Chance::GetByIDOrName($id);
		
		if ($chance == null)
		{
			$errorPage = new PsychaticaErrorPage();
			$errorPage->Message = "The specified Chance does not exist, or the duration for this event has passed.";
			$errorPage->ReturnButtonURL = "~/market/chances";
			$errorPage->ReturnButtonText = "Return to Chances";
			$errorPage->Render();
			return;
		}
		
		require("detail.inc.php");
		return;
	}
	else
	{
		require("list.inc.php");
		return;
	}
?>