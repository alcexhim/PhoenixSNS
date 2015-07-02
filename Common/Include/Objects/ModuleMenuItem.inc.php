<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	/**
	 * A menu item provided by a Module. Menu items are generally displayed on the application menu ("hamburger" or
	 * "ribbon" menu).
	 * 
	 * @author Michael Becker
	 */
	class ModuleMenuItem
	{
		/**
		 * The unique, incremental ID number of this ModuleMenuItem
		 * @var int
		 */
		public $ID;
		
		/**
		 * The title of this menu item
		 * @var string
		 */
		public $Title;
		
		/**
		 * A short description of this menu item
		 * @var string
		 */
		public $Description;
		
		/**
		 * The URL to navigate to when this menu item is activated
		 * @var string
		 */
		public $TargetURL;
		
		/**
		 * The script code to run when this menu item is activated
		 * @var string
		 */
		public $TargetScript;
		
		/**
		 * The ModuleMenuItem which contains this ModuleMenuItem (e.g. a dropdown)
		 * @var ModuleMenuItem
		 */
		public $ParentMenuItem;
		
		public static function GetByAssoc($values)
		{
			$item = new ModuleMenuItem();
			$item->ID = $values["menuitem_ID"];
			$item->Title = $values["menuitem_Title"];
			$item->Description = $values["menuitem_Description"];
			$item->TargetURL = $values["menuitem_TargetURL"];
			$item->TargetScript = $values["menuitem_TargetScript"];
			if ($values["menuitem_ParentMenuItemID"] != null)
			{
				$item->ParentMenuItem = ModuleMenuItem::GetByAssoc($values["menuitem_ParentMenuItemID"]);
			}
			return $item;
		}
	}
?>