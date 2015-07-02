<?php
	class Game
	{
		public $ID;
		public $Name;
		public $Title;
		public $Description;
		public $Content;
		public $Creator;
		public $TimestampCreated;
		public $PlayerCountMinimum;
		public $PlayerCountMaximum;
		public $RequiredResources;
		
		public static function Count()
		{
			$query = "SELECT COUNT(*) FROM phpmmo_arcade_games";
			$result = mysql_query($query);
			$values = mysql_fetch_array($result);
			return $values[0];
		}
		public static function GetByAssoc($values)
		{
			$game = new Game();
			$game->ID = $values["game_id"];
			$game->Name = $values["game_name"];
			$game->Title = $values["game_title"];
			$game->Description = $values["game_description"];
			$game->Content = $values["game_content"];
			$game->Creator = User::GetByID($values["game_creator_id"]);
			$game->TimestampCreated = $values["game_timestamp_created"];
			$game->PlayerCountMaximum = $values["game_player_count_max"];
			$game->PlayerCountMinimum = $values["game_player_count_min"];
			
			$resources = array();
			$query = "SELECT * FROM phpmmo_arcade_game_resource_costs WHERE game_id = " . $game->ID;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$resource = mmo_get_resources_by_assoc($values);
				$resources[] = $resource;
			}
			$game->RequiredResources = $resources;
			
			return $game;
		}
		public static function Get($playerCountMinimum = null, $playerCountMaximum = null, $max = null)
		{
			$query = "SELECT * FROM phpmmo_arcade_games WHERE 1=1";
			if ($playerCountMinimum != null) $query .= " AND game_player_count_min >= " . $playerCountMinimum;
			if ($playerCountMaximum != null) $query .= " AND game_player_count_max <= " . $playerCountMaximum;
			$query .= " ORDER BY game_title";
			if ($max != null) $query .= " LIMIT " . $max;
			
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = Game::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByIDOrName($idOrName)
		{
			if (is_numeric($idOrName)) return Game::GetByID($idOrName);
			return Game::GetByName($idOrName);
		}
		public static function GetByID($game_id)
		{
			$query = "SELECT * FROM phpmmo_arcade_games WHERE game_id = " . $game_id;
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			return Game::GetByAssoc($values);
		}
		public static function GetByName($game_name)
		{
			$query = "SELECT * FROM phpmmo_arcade_games WHERE game_name = '" . mysql_real_escape_string($game_name) . "'";
			$result = mysql_query($query);
			$values = mysql_fetch_assoc($result);
			return Game::GetByAssoc($values);
		}
	}
	class GamePlayer
	{
	}
	class ArcadeRedemptionItem
	{
		public $Item;
		public $TicketCount;
		
		public static function Get()
		{
			$query = "SELECT * FROM phpmmo_arcade_redemption_items";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = mysql_fetch_assoc($result);
				$retval[] = ArcadeRedemptionItem::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByAssoc($values)
		{
			
		}
	}
?>