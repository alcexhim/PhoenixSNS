<?php
	namespace PhoenixSNS\Controls;
	
	use WebFX\Controls\TextBox;
	use WebFX\System;
	
	class SearchTextBox extends TextBox
	{
		public $SearchScopes;
		
		public function __construct($id)
		{
			parent::__construct($id);
			$this->SearchScopes = array();
		}
		
		protected function OnInitialize()
		{
			parent::OnInitialize();
			$url = "~/API/Search.php?format=json&query=%1";
			$count = count($this->SearchScopes);
			if ($count > 0)
			{
				$url .= "&include=";
				for ($i = 0; $i < $count; $i++)
				{
					$scope = $this->SearchScopes[$i];
					$url .= $scope;
					if ($i < $count - 1) $url .= ",";
				}
			}
			$this->SuggestionURL = System::ExpandRelativePath($url);
		}
		protected function AfterContent()
		{
		?>
			<script type="text/javascript">
				<?php echo($this->ID); ?>.FormatStart = function()
				{
					var html = "";
					// html += "<img src=\"/images/logowntr.png\" style=\"width: 320px; display: block;\" />";
					html += "<div class=\"Menu\" style=\"max-height: 300px;\">";
					return html;
				};
				
				var lastItemCategory = "";
				<?php echo($this->ID); ?>.FormatItem = function(item)
				{
					var html = "";
					if (lastItemCategory != item.Category)
					{
						html += "<h2>" + item.Category + "</h2>";
					}
					lastItemCategory = item.Category;
					switch (item.Category)
					{
						case "Tasks":
						{
							html += "<a class=\"MenuItem\" href=\"" + System.ExpandRelativePath(item.Item.NavigateURL) + "\">" + item.Item.Title + "</a>";
							break;
						}
						case "Administrative Tasks":
						{
							html += "<a class=\"MenuItem\" href=\"" + System.ExpandRelativePath(item.Item.NavigateURL) + "\">" + item.Item.Title + "</a>";
							break;
						}
						case "Members":
						{
							html += "<a class=\"MenuItem\" href=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/" + item.Item.ShortName + "\"><img src=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/" + item.Item.ShortName + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.LongName + "</a>";
							break;
						}
						case "Groups":
						{
							html += "<a class=\"MenuItem\" href=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/groups/" + item.Item.Name + "\"><img src=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/groups/" + item.Item.Name + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.Title + "</a>";
							break;
						}
						case "Pages":
						{
							html += "<a class=\"MenuItem\" href=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/pages/" + item.Item.Name + "\"><img src=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/groups/" + item.Item.Name + "/images/avatar/thumbnail.png\" style=\"width: 24px;\" /> " + item.Item.Title + "</a>";
							break;
						}
						case "Places":
						{
							html += "<a class=\"MenuItem\" href=\"<?php echo(System::$Configuration["Application.BasePath"]); ?>/world/" + item.Item.Name + "\">" + item.Item.Title + "</a>";
							break;
						}
					}
					return html;
				};
				<?php echo($this->ID); ?>.FormatEnd = function()
				{
					lastItemCategory = "";
					return "</div>";
				};
			</script>
		<?php
		}
	}
?>