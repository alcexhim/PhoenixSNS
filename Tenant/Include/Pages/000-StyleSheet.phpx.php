<?php
	namespace PhoenixSNS\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Phast\Compilers\StyleSheet\Internal\LessStyleSheetCompiler;
	use Phast\Compilers\StyleSheet\Internal\Formatters\CompressedFormatter;
	
	use Phast\System;
		
	class StyleSheetPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			header("Content-Type: text/css");
			
			$path = System::GetApplicationPath() . "/Resources/" . $e->RenderingPage->GetPathVariableValue("BundleName") . "/StyleSheets/*.less";
			$filenames = glob($path);
			
			$content = "";
			foreach ($filenames as $filename)
			{
				$content .= "/* " . $filename . " */";
				$content .= file_get_contents($filename);
				$content .= "\r\n";
			}
				
			try
			{
				$less = new LessStyleSheetCompiler();
				$less->formatter = new CompressedFormatter();
				$v = $less->compile($content);
				
				echo("/* compiled with lessphp v0.4.0 - GPLv3/MIT - http://leafo.net/lessphp */\n");
				echo("/* for human-readable source of this file, replace .css with .less in the file name */\n");
				echo($v);
			}
			catch (Exception $e)
			{
				echo "/* " . $e->getMessage() . " */\n";
			}
			$e->Cancel = true;
		}
	} 
?>