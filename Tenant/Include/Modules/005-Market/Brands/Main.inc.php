<?php
	if (count($path) > 1 && $path[1] != "")
	{
		$id = $path[1];
		$brand = null; // Brand::GetByIDOrName($id);
		
		if ($brand == null)
		{
			$errorPage = new PsychaticaErrorPage();
			$errorPage->Message = "The specified Brand does not exist.";
			$errorPage->ReturnButtonURL = "~/market/brands";
			$errorPage->ReturnButtonText = "Return to Brands";
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