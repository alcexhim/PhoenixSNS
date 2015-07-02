<?php
	namespace PhoenixSNS\Objects;
	
	use Phast\System;
	
	use Phast\Data\DataSystem;
	use Phast\Data\Table;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	
	use Phast\Enumeration;

	/**
	 * Contains constants that define a user's profile visibility.
	 * @author Michael Becker
	 * @todo Move this into a generic SecurableObject property SecurableObjectVisibility
	 */
	class UserProfileVisibility extends Enumeration
	{
		/**
		 * The user's profile is hidden from everyone (except the profile owner).
		 * @var int 0
		 */
		const Hidden = 0;
		/**
		 * The user's profile is visible to everyone on the Internet.
		 * @var int 1
		 */
		const Everyone = 1;
		/**
		 * The user's profile is only visible to members of this social network.
		 * @var int 2
		 */
		const Sitewide = 2;
		/**
		 * The user's profile is visible to their friends and the friends of their friends.
		 * @var int 3
		 */
		const ExtendedFriends = 3;
		/**
		 * The user's profile is visible only to people who are already friends with that user.
		 * @var int 4
		 */
		const Friends = 4;
	}
	
	/**
	 * Contains constants that define a user's presence status.
	 * @author Michael Becker
	 */
	class UserPresenceStatus extends Enumeration
	{
		const Offline = 0;
		const Available = 1;
		const Away = 2;
		const ExtendedAway = 3;
		const Busy = 4;
		const Hidden = 5;
	}
	
	class UserProfileContent
	{
		public $Description;
		public $Likes;
		public $Dislikes;
	}
	class UserPresence
	{
		public $Message;
		public $Status;
		
		public function ToString()
		{
			echo("<span title=\"" . $this->Message . "\" class=\"Presence");
			switch ($this->Status)
			{
				case UserPresenceStatus::Offline: // offline
				case UserPresenceStatus::Hidden: // offline
				{
					echo(" PresenceOffline");
					break;
				}
				case UserPresenceStatus::Available: // available
				{
					echo(" PresenceAvailable");
					break;
				}
				case UserPresenceStatus::Away: // away
				case UserPresenceStatus::ExtendedAway: // away
				{
					echo(" PresenceAway");
					break;
				}
				case UserPresenceStatus::Busy: // busy
				{
					echo(" PresenceBusy");
					break;
				}
			}
			echo("\">&nbsp;</span>");
		}
	}
	class User
	{
		/**
		 * The unique identifier used to identify this User in the database.
		 * @var int
		 */
		public $ID;
		/**
		 * The private login name used by this User to log into the system.
		 * @var string
		 */
		public $UserName;
		/**
		 * The short name used publicly in URLs around the site.
		 * @var string
		 */
		public $ShortName;
		/**
		 * The long name displayed publicly around the site.
		 * @var string
		 */
		public $LongName;
		
		public $EmailAddress;
		public $BirthDate;
		public $RealName;
		public $Password;
		public $IsAuthenticated;
		public $ProfileContent;
		public $Theme;
		public $RegistrationDate;
		public $RegistrationIPAddress;
		public $LastLoginDate;
		public $ConsecutiveLoginCount;
		public $ConsecutiveLoginAttempts;
		public $Language;
		
		/**
		 * The user's presence status information.
		 * @var UserPresence
		 */
		public $Presence;
		
		/**
		 * The visibility of this user's profile.
		 * @var UserProfileVisibility
		 */
		public $ProfileVisibility;
		
		public function DailyReward()
		{
			
		}
		
		public function IsVisible()
		{
			$CurrentUser = User::GetCurrent();
			$visible = false;
			if ($CurrentUser != null)
			{
				$visible = ($this->ID == $CurrentUser->ID);
				if (!$visible)
				{
					if ($this->IsBlocked()) return false;
					if ($this->ProfileVisibility == UserProfileVisibility::Friends && $CurrentUser != null)
					{
						// friends only
						$visible = ($this->HasFriend($CurrentUser));
					}
					else if ($this->ProfileVisibility == UserProfileVisibility::Sitewide)
					{
						// any member of psychatica
						$visible = ($CurrentUser != null);
					}
				}
			}
			else if ($this->ProfileVisibility == UserProfileVisibility::Everyone)
			{
				$visible = true;
			}
			return $visible;
		}
		
		public function Login()
		{
			// TODO: this should all be replaced eventually
			global $MySQL;
			
			$query = "SELECT (DATE(user_LastLoginTimestamp) < DATE(NOW())) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_ID = " . $user->ID;
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if ($values[0] == 1)
			{
				// this is the first time we've logged in today
				DailyReward();
			}
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			// update user presence in the database
			$MySQL->query("UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET user_ConsecutiveLoginFailures = 0, user_LastLoginTimestamp = NOW(), user_PresenceStatus = 1 WHERE user_ID = " . $user->ID);
			
			// set registration IP address if it wasn't set properly on registration (though it should be...)
			$MySQL->query("UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET user_RegistrationIPAddress = '" . $_SERVER["REMOTE_ADDR"] . "' WHERE user_ID = " . $user->ID . " AND (user_RegistrationIPAddress = '0.0.0.0' OR user_RegistrationIPAddress IS NULL)");
			
			// create a new record for this user in the user logins table
			$MySQL->query("INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "UserLogins (userlogin_UserID, userlogin_IPAddress, userlogin_Timestamp) VALUES (" . $this->ID . ", '" . $_SERVER["REMOTE_ADDR"] . "', NOW())");
			
			return true;
		}
		public function Logout()
		{
			global $MySQL;
			$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET user_PresenceStatus = 0 WHERE user_ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($result === false) return false;
			return true;
		}
		
		public function EmbedAvatar($user = null, $x = null, $y = null)
		{
			echo($this->GetAvatarContent($user, $x, $y));
		}
		
		public function GetAvatarContent($user = null, $x = null, $y = null)
		{
			if ($user == null) $user = User::GetCurrent();
			$retval = "";
			$retval .= "<div class=\"Avatar\"";
			$retval .= " style=\"";
			if ($x != null || $y != null)
			{
				$retval .= "position: absolute; ";
				if ($x != null)
				{
					$retval .= "left: " . $x . "px;";
				}
				if ($y != null)
				{
					$retval .= "top: " . $y . "px;";
				}
			}
			$retval .= "\">";
			
			/*
				<noscript>
					<div class="AvatarPassive">
					</div>
					<div class="AvatarPassiveWarning">Passive Mode (<a href="/content/help/javascript">help</a>)</div>
				</noscript>
				<div class="AvatarActive">
					<img class="AvatarLoadingImage" id="Avatar_MyAvatar_LoadingImage" src="/images/avatar/base.gif" alt="Loading" />
					<script type="text/javascript">
						// Load in the active avatar.
						// var avatar = new Avatar("MyAvatar");
					</script>
				</div>
			*/
			$retval .= "<div class=\"AvatarPassive\" style=\"position: relative; background-image: url('" . System::ExpandRelativePath("~/community/members/" . $this->ShortName . "/images/avatar/preview.png") . "');\">";
			
			$retval .= "</div>";
			$retval .= "</div>";
			return $retval;
		}
		
		public function Block($reason = null, $hidereason = false)
		{
			global $MySQL;
			$CurrentUser = User::GetCurrent();
			
			$table = Table::Get("UserBlockedList", "blocklist");
			$table->Insert
			(
				new Record(array
				(
						new RecordColumn("MemberID", $CurrentUser->ID),
						new RecordColumn("BlockedMemberID", $this->ID),
						new RecordColumn("BlockedTimestamp", ColumnValue::Now),
						new RecordColumn("BlockedReason", $reason),
						new RecordColumn("BlockedReasonVisible", ($hidereason ? false : true))
				))
			);
			
			if ($result->errno != 0) return false;
			
			return true;
		}
		public function IsBlocked($byWhom = null)
		{
			if ($byWhom == null) $byWhom = User::GetCurrent();
			if ($byWhom == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserBlockedUsers WHERE (blocklist_UserID = " . $byWhom->ID . " AND blocklist_BlockedUserID = " . $this->ID . ") OR (blocklist_UserID = " . $this->ID . " AND blocklist_BlockedUserID = " . $byWhom->ID . ")";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return ($values[0] > 0);
			return false;
		}
		
		public function Report()
		{
		}
		
		public function HasFriend($friend)
		{
			if ($friend == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::$Configuration["Database.TablePrefix"] . "UserFriends WHERE ((userfriend_UserID = " . $this->ID . " AND userfriend_FriendID = " . $friend->ID . ") OR (userfriend_FriendID = " . $this->ID . " AND userfriend_UserID = " . $friend->ID . ")) AND userfriend_FriendshipAuthorizationKey IS NULL";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			return ($values[0] > 0);
		}
		
		public function ToJSON()
		{
			$json = "{";
			$json .= "\"ID\": " . $this->ID . ",";
			$json .= "\"ShortName\": \"" . \JH\Utilities::JavaScriptEncode($this->ShortName) . "\",";
			$json .= "\"LongName\": \"" . \JH\Utilities::JavaScriptEncode($this->LongName) . "\"";
			$json .= "}";
			return $json;
		}
		
		public static function Create($member_username, $password, $member_longname, $user_URLName, $member_email, $member_emailcode = null)
		{
			$CurrentTenant = Tenant::GetCurrent();
			if ($CurrentTenant == null) return null;
			
			$passwordSalt = \UUID::Generate();
			$passwordHash = hash("sha512", $passwordSalt . $password);
			
			global $MySQL;
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Users (user_TenantID, user_LoginID, user_PasswordHash, user_PasswordSalt, user_DisplayName, user_URLName, user_EmailAddress, user_EmailConfirmationCode, user_RegistrationTimestamp, user_RegistrationIPAddress) VALUES (" .
				$CurrentTenant->ID . ", " .
				"'" . $MySQL->real_escape_string($member_username) . "', " .
				"'" . $MySQL->real_escape_string($passwordHash) . "', " .
				"'" . $MySQL->real_escape_string($passwordSalt) . "', " .
				"'" . $MySQL->real_escape_string($member_longname) . "', " .
				"'" . $MySQL->real_escape_string($user_URLName) . "', " .
				"'" . $MySQL->real_escape_string($member_email) . "', " .
				($member_emailcode == null ? "NULL" : ("'" . $MySQL->real_escape_string($member_emailcode) . "'")) . ", " .
				"NOW()," .
				"'" . $_SERVER["REMOTE_ADDR"] . "'" .
			")";
			
			$result = $MySQL->query($query);
			if ($MySQL->errno == 0)
			{
				$id = $MySQL->insert_id;
				$user = User::GetByID($id, true);
				
				MarketResourceTransaction::Create(MarketResourceTransaction::GetInitialResources(), $user, null, "InitialResourceAllotmentForUser:" . $user->ID, null);
				return $user;
			}
			return null;
		}
		
		public static function ValidateShortName($name)
		{
			if ($name == null) return "You must provide a short name for use with URLs.";
			
			$result = null;
			if (!ctype_alnum(str_replace(array('-', '_', '.'), '', $name))) $result = "Short name must consist of only alphanumeric characters (0-9, A-Z, a-z), dash (-), period (.), or underscore (_).";
			return $result;
		}
		
		public $OutfitCacheTimestamp;
		
		public function PurchaseItem($entry, $quantity = 1)
		{
			if ($entry == null) return false;
			
			// Check to see if we have enough resources to purchase the item.
			$resources = $entry->GetRequiredResources();
			foreach ($resources as $resource)
			{
				$myResourceAmount = MarketResourceTransaction::GetAmountByUser($this, $resource->Type);
				if ($myResourceAmount < $resource->Amount) return false;
			}
			
			// If we've reached here, then we are not bankrupt. Remove the resources from the user's wallet. The syntax is (from MarketResourceTransaction.inc.php):
			// 		public static function Create($resources, $receiver = null, $sender = null, $comments = null, $creationUser = null)
			$tresources = array();
			foreach ($resources as $resource)
			{
				$tresources[] = new MarketResourceTransactionResource
				(
					$resource->Type,
					-1 * $resource->Amount
				);
			}
			MarketResourceTransaction::Create($resources, $user, null, "MarketItemPurchase:" . $entry->Item->ID, null);
			
			global $MySQL;
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "UserInventoryItems (inventoryitem_UserID, inventoryitem_ItemID) VALUES (" . $this->ID . ", " . $entry->Item->ID . ")";
			$result = $MySQL->query($query);
			
			if (!$result) return false;
			return true;
		}
		
		public function CountInventoryItems($item = null)
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserInventoryItems WHERE useritem_UserID = " . $this->ID;
			if ($item != null)
			{
				$query .= " AND useritem_ItemID = " . $item->ID;
			}
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		public function HasInventoryItem($item)
		{
			if ($item == null) return false;
			
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserInventoryItems WHERE useritem_UserID = " . $this->ID . " AND useritem_ItemID = " . $item->ID;
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return ($values[0] > 0);
			return false;
		}
		
		public function GetURL()
		{
			$url = System::ExpandRelativePath("~/community/members/");
			if (User::ValidateShortName($this->ShortName))
			{
				$url .= $this->ShortName;
			}
			else
			{
				$url .= $this->ID;
			}
			return $url;
		}
		
		public static function Search($searchQuery, $max = null, $orderBy = null, $showAll = false)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE 1=1";
			if ($searchQuery != "")
			{
				$query .= " AND user_DisplayName LIKE '%" . $MySQL->real_escape_string($searchQuery) . "%'";
			}
			if (!$showAll) $query .= " AND user_EmailConfirmationCode IS NULL";
			if ($orderBy != null) $query .= " ORDER BY " . $orderBy;
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$retval = array();
			if ($result->errno != 0) return $retval;
			
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$user = User::GetByAssoc($values, $showAll);
				if ($user == null) continue;
				$retval[] = $user;
			}
			return $retval;
		}
		
		
		public static function Count($includeAll = false)
		{
			return count(User::Get(null, $includeAll));
			// $query = "SELECT COUNT(" . System::GetConfigurationValue("Database.TablePrefix") . "Users.user_ID), " . System::GetConfigurationValue("Database.TablePrefix") . "Users.user_ID, " . System::GetConfigurationValue("Database.TablePrefix") . "blocked_members FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users, " . System::GetConfigurationValue("Database.TablePrefix") . "member_blocked_members WHERE member_emailcode IS NULL AND NOT user_ID IN  FROM " . System::GetConfigurationValue("Database.TablePrefix") . "member_blocked_users WHERE user_ID = " . $this->ID . ")";
			// $result = $MySQL->query($query);
			// $values = $result->fetch_array();
			// return $values[0];
		}
		
		public static function Get($max = null, $includeAll = false)
		{
			$users = array();
			
			$CurrentTenant = Tenant::GetCurrent();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_TenantID = " . $CurrentTenant->ID;
			if (!$includeAll) $query .= " AND user_EmailConfirmationCode IS NULL";
			
			$query .= " ORDER BY user_DisplayName";
			if ($max != null && is_numeric($max)) $query .= " LIMIT " . $max;
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$user = User::GetByAssoc($values, $includeAll);
				if ($user == null) continue;
				$users[] = $user;
			}
			return $users;
		}
		public static function GetByEmail($email, $allusers = false)
		{
			if ($email == null) return array();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_EmailAddress = '" . $MySQL->real_escape_string($email) . "'";
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$user = User::GetByAssoc($values, $allusers);
				if ($user == null) continue;
				$retval[] = $user;
			}
			return $retval;
		}
		public static function GetByLoginID($userName, $allusers = false)
		{
			if ($userName == null) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_LoginID = '" . $MySQL->real_escape_string($userName) . "'";
			// if (!$allusers) $query .= " AND user_EmailConfirmationCode IS NULL";

			$result = $MySQL->query($query);
			
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return User::GetByAssoc($values, $allusers);
			}
			return null;
		}
		public static function GetByIDOrShortName($idOrName, $includeAll = false)
		{
			if (is_numeric($idOrName))
			{
				return User::GetByID($idOrName, $includeAll);
			}
			return User::GetByShortName($idOrName, $includeAll);
		}
		public static function GetByAssoc($values, $allusers = false)
		{
			$user = new User();
			$user->ID = $values["user_ID"];
			$user->UserName = $values["user_LoginID"];
			$user->ShortName = $values["user_URLName"];
			$user->LongName = $values["user_DisplayName"];
			$user->EmailAddress = $values["user_EmailAddress"];
			$user->BirthDate = $values["user_BirthDate"];
			$user->RealName = $values["user_RealName"];
			
			$user->Theme = Theme::GetByID($values["user_ThemeID"]);
			$user->ConsecutiveLoginCount = $values["user_ConsecutiveLoginCount"];
			$user->ConsecutiveLoginFailures = $values["user_ConsecutiveLoginFailures"];
			$user->LastLoginDate = $values["user_LastLoginTimestamp"];
			$user->RegistrationDate = $values["user_RegistrationTimestamp"];
			$user->RegistrationIPAddress = $values["user_RegistrationIPAddress"];
			
			if (isset($_SESSION["CurrentUserID"]))
			{
				$user->IsAuthenticated = ($_SESSION["CurrentUserID"] == $user->ID);
			}
			else
			{
				$user->IsAuthenticated = false;
			}
			$user->Language = Language::GetByID($values["user_LanguageID"]);
			
			$user->ProfileVisibility = UserProfileVisibility::FromIndex($values["user_ProfileVisibility"]);
			
			$presence = new UserPresence();
			$presence->Status = $values["user_PresenceStatus"];
			$presence->Message = $values["user_PresenceMessage"];
			$user->Presence = $presence;
			
			$user->StartPage = StartPage::GetByID($values["user_StartPageID"]);
			$user->LastLoginDate = $values["user_LastLoginTimestamp"];
			
			if (!$allusers)
			{
				// check for blocked users
				if (isset($_SESSION["CurrentUserID"]))
				{
					if ($user->ID != $_SESSION["CurrentUserID"])
					{
						if (!$user->IsVisible()) return null;
					}
				}
			}
			return $user;
		}
		public static function GetByCredentials($userName, $password = null, $allusers = false)
		{
			if ($userName == null) return null;
			global $MySQL;
			
			$CurrentTenant = Tenant::GetCurrent();
			if ($CurrentTenant == null) return null;
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_LoginID = '" . $MySQL->real_escape_string($userName) . "' AND user_TenantID = " . $CurrentTenant->ID;
			// if (!$allusers) $query .= " AND member_emailcode IS NULL";
			
			if ($password != null)
			{
				$querySalt = "SELECT user_PasswordSalt FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_LoginID = '" . $MySQL->real_escape_string($userName) . "' AND user_TenantID = " . $CurrentTenant->ID;
				$resultSalt = $MySQL->query($querySalt);
				if ($resultSalt === false) return null;
				
				$valuesSalt = $resultSalt->fetch_array();
				$passwordSalt = $valuesSalt[0];
				
				$query .= " AND user_PasswordHash = '" . hash("sha512", $passwordSalt . $password) . "'";
			}
			$result = $MySQL->query($query);
			
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return User::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByID($id, $includeAll = false)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_ID = " . $id;
			// if (!$includeAll) $query .= " AND member_emailcode IS NULL";
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return User::GetByAssoc($values);
			}
			return null;
		}
		public static function GetByShortName($shortName, $includeAll = false)
		{
			if ($shortName == null) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Users WHERE user_URLName = '" . $MySQL->real_escape_string($shortName) . "'";
			//if (!$includeAll) $query .= " AND member_emailcode IS NULL";
			
			$result = $MySQL->query($query);
			if ($result->num_rows > 0)
			{
				$values = $result->fetch_assoc();
				return User::GetByAssoc($values);
			}
			return null;
		}
		public static function GetCurrent()
		{
			$CurrentTenant = Tenant::GetCurrent();
			if ($CurrentTenant == null) return null;
			
			// prevent a NOTICE from cluttering the log
			if (!isset($_SESSION["CurrentUserName[" . $CurrentTenant->ID . "]"]) ||!isset($_SESSION["CurrentPassword[" . $CurrentTenant->ID . "]"])) return null;
			
			return User::GetByCredentials($_SESSION["CurrentUserName[" . $CurrentTenant->ID . "]"], $_SESSION["CurrentPassword[" . $CurrentTenant->ID . "]"]);
		}
		
		public static function GetRandom()
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users ORDER BY RAND() LIMIT 1";
			$result = $MySQL->query($query);
			$values = $result->fetch_assoc();
			return User::GetByAssoc($values);
		}
		
		public function CountFriends($includeOffline = true)
		{
			global $MySQL;
			$query = "SELECT DISTINCT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.FriendshipAuthorizationKey IS NULL AND ((" . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_UserID = " . $this->ID . ") OR (" . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID = " . $this->ID . "))";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			if (is_numeric($values[0])) return $values[0];
			return 0;
		}
		public function GetFriends($includeOffline = true, $max = null)
		{
			$friends = array();
			
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendshipAuthorizationKey IS NULL AND ((" . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_UserID = " . $this->ID . ") OR (" . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID = " . $this->ID . "))";
			if ($max != null && is_numeric($max))
			{
				$query .= " LIMIT " . $max;
			}
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				if ($values["user_ID"] == $this->ID)
				{
					$user = User::GetByID($values["userfriend_FriendID"]);
					if ($user == null) continue;
					
					$friend = new Friend();
					$friend->User = $user;
					$friend->Timestamp = $values["userfriend_FriendshipTimestamp"];
				}
				else
				{
					$user = User::GetByID($values["user_ID"]);
					if ($user == null) continue;
					
					$friend = new Friend();
					$friend->User = $user;
					$friend->Timestamp = $values["userfriend_FriendshipTimestamp"];
				}
				if (!in_array($friend, $friends) && ($includeOffline || ($friend->User->Presence->Status >= 1 && $friend->User->Presence->Status <= 3)))
				{
					$friends[] = $friend;
				}
			}
			return $friends;
		}
		public function GetFriendCircles($max = null)
		{
			return FriendCircle::GetByUser($this);
		}
		public function SetPresence($status, $message = null)
		{
			if (!is_numeric($status)) return false;
			if ($status < 1) return false;
			
			global $MySQL;
			$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET user_PresenceStatus = " . $status;
			if ($message != null) $query .= ", user_PresenceMessage = '" . $MySQL->real_escape_string($message) . "'";
			$query .= " WHERE user_ID = " . $this->ID;
			
			$result = $MySQL->query($query);
			if ($result->errno != 0) return false;
			
			$this->Presence->Status = $status;
			$this->Presence->Message = $message;
			return true;
		}
		
		public function PasswordRequiresReset()
		{
			global $MySQL;
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_ID = " . $MySQL->real_escape_string($this->ID) . " AND user_PasswordHash IS NULL";
			$result = $MySQL->query($query);
			$values = $result->fetch_array();
			return ($values[0] > 0);
		}
		
		public function ToString()
		{
			$value = "<span class=\"ProfileBadges\">";
			$badges = Badge::GetByUser($this);
			foreach ($badges as $badge)
			{
				$value .= "<img src=\"" . System::ExpandRelativePath("~/images/badges/" . $badge->Name . ".png") . "\" alt=\"" . $badge->Title . "\" title=\"" . $badge->Title . "\" /> ";
			}
			$value .= "</span>";
			$value .= $this->LongName;
			return $value;
		}
		
		
		public function Update()
		{
			$valid_shortname = User::ValidateShortName($this->ShortName);
			if ($valid_shortname != null)
			{
				mmo_error_set($valid_shortname);
				return false;
			}
			
			global $MySQL;
			$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET ";
			$query .= "user_RealName = '" . $MySQL->real_escape_string($this->RealName) . "', ";
			$query .= "user_URLName = '" . $MySQL->real_escape_string($this->ShortName) . "', ";
			$query .= "user_DisplayName = '" . $MySQL->real_escape_string($this->LongName) . "', ";
			$query .= "user_BirthDate = '" . $MySQL->real_escape_string($this->BirthDate) . "', ";
			$query .= "user_EmailAddress = '" . $MySQL->real_escape_string($this->EmailAddress) . "', ";
			$query .= "user_ProfileVisibility = " . UserProfileVisibility::ToIndex($this->ProfileVisibility);
			
			$query .= " WHERE user_ID = " . $this->ID;
			
			$MySQL->query($query);
			return ($result->errno == 0);
		}
	}
	class Friend
	{
		public $User;
		public $Timestamp;
	}
	class FriendCircle
	{
		public $ID;
		public $Title;
		
		public function GetFriends($max = null)
		{
			global $MySQL;
			$query = "SELECT " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID, " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.friendship_timestamp FROM " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circle_members, " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends, " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circles WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circle_members.circle_id = " . $this->ID . " AND " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circles.circle_id = " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circle_members.circle_id AND " .
			
			"(" . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circle_members.user_ID = " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID AND " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circles.owner_id = " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_UserID)";
			
			if ($max != null && is_numeric($max))
			{
				$query .= " LIMIT " . $max;
			}
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				
				$user = User::GetByID($values["userfriend_FriendID"]);
				if ($user == null) continue;
				
				$friend = new Friend();
				$friend->User = User::GetByID($values["userfriend_FriendID"]);
				$friend->Timestamp = $values["friendship_timestamp"];
				$retval[] = $friend;
			}
			return $retval;
		}
		
		public static function GetByAssoc($values)
		{
			$circle = new FriendCircle();
			$circle->ID = $values["circle_id"];
			$circle->Title = $values["circle_title"];
			return $circle;
		}
		public static function GetByUser($user, $max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "member_friend_circles WHERE owner_id = " . $user->ID;
			if ($max != null && is_numeric($max))
			{
				$query .= " LIMIT " . $max;
			}
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = FriendCircle::GetByAssoc($values);
			}
			return $retval;
		}
	}
	
	function mmo_get_user_by_username($userName, $password = null, $allusers = false)
	{
		if ($userName == null) return null;
		
		$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE member_username = '" . $MySQL->real_escape_string($userName) . "'";
		if (!$allusers) $query .= " AND member_emailcode IS NULL";
		
		if ($password != null)
		{
			$query .= " AND member_password = '" . hash("sha512", $password) . "'";
		}
		$result = $MySQL->query($query);
		
		if ($result->num_rows > 0)
		{
			$values = $result->fetch_assoc();
			return User::GetByAssoc($values);
		}
		return null;
	}
	
	function mmo_get_user_friend_requests($user = null, $max = null)
	{
		$friends = array();
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return $friends;
		
		$query = "SELECT " . System::GetConfigurationValue("Database.TablePrefix") . "Users.user_ID, " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_UserID, " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID, " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.friendship_auth_key FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users, " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.friendship_auth_key IS NOT NULL AND " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID = " . $user->ID . " AND " . System::GetConfigurationValue("Database.TablePrefix") . "Users.user_ID = " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID";
		if ($max != null && is_numeric($max))
		{
			$query .= " LIMIT " . $max;
		}
		$result = $MySQL->query($query);
		$count = $result->num_rows;
		for ($i = 0; $i < $count; $i++)
		{
			$values = $result->fetch_assoc();
			$friend = User::GetByID($values["user_ID"]);
			if ($friend == null) continue;
			$friends[] = $friend;
		}
		return $friends;
	}
	function mmo_get_user_friend_requests_count($user = null)
	{
		$friends = array();
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return $friends;
		
		$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.friendship_auth_key IS NOT NULL AND " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends.userfriend_FriendID = " . $user->ID;
		$result = $MySQL->query($query);
		$values = $result->fetch_array();
		return $values[0];
	}
	function mmo_has_user_friend_request($user = null, $friend)
	{
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return false;
		
		$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE user_ID = " . $user->ID . " AND userfriend_FriendID = " . $friend->ID . " AND friendship_auth_key IS NOT NULL";
		$result = $MySQL->query($query);
		$values = $result->fetch_array();
		return ($values[0] > 0);
	}
	function mmo_get_user_friendship_auth_key($user = null, $friend)
	{
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return false;
		
		$query = "SELECT friendship_auth_key FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE user_ID = " . $user->ID . " AND userfriend_FriendID = " . $friend->ID;
		$result = $MySQL->query($query);
		$values = $result->fetch_array();
		return $values[0];
	}
	function mmo_send_user_friend_request($user = null, $friend, $message = null)
	{
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return false;
		
		$authkey = get_random_string("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890", 16);
		$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends (user_ID, userfriend_FriendID, friendship_auth_key) VALUES (" . $user->ID . ", " . $friend->ID . ", '" . $authkey . "');";
		$result = $MySQL->query($query);
		
		// notify the user that he wants to be friends
		$msg = "";
		if ($message != null)
		{
			$msg = "&quot;" . $message . "&quot;<br />";
		}
		$msg .= "<a href=\"" . System::ExpandRelativePath("~/community/members/" . $user->ShortName) . "\">Go to my profile</a> to confirm the request.";
		
		Notification::Create($friend, "I would like to be your friend!", $msg, $user);
		return true;
	}
	function mmo_accept_user_friend_request($user = null, $friend, $authkey)
	{
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return false;
		
		$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends SET friendship_auth_key = NULL, friendship_timestamp = NOW() WHERE (user_ID = " . $user->ID . " AND userfriend_FriendID = " . $friend->ID . ") OR (user_ID = " . $friend->ID . " AND userfriend_FriendID = " . $user->ID . ") AND friendship_auth_key = '" . $MySQL->real_escape_string($authkey) . "'";
		$result = $MySQL->query($query);
		
		/*
		mail($member_email, $friend->LongName . " has accepted your friend request!",
		"<html>" .
		"<body>" .
		"<p>Hello, " . $user->LongName . ".</p>" .
		"<p>Your friend " . $friend->LongName . " has approved your request to be placed on their friends list.</p>" .
		"</body>" .
		"</html>",
		"From: notifications@alceproject.net\r\nContent-type: text/html; charset=UTF-8");
		*/
		
		// notify the user that he wants to be friends
		$msg .= "Stop by and say hi! <a href=\"" . System::ExpandRelativePath("~/community/members/" . $friend->ShortName) . "\">Write a Shoutout</a>";
		
		//					receiver									sender
		Notification::Create($user, "I am now friends with you!", $msg, $friend);
		return true;
	}
	function mmo_withdraw_user_friend_request($user = null, $friend)
	{
		if ($user == null) $user = User::GetCurrent();
		if ($user == null) return false;
		
		$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserFriends WHERE (user_ID = " . $user->ID . " AND userfriend_FriendID = " . $friend->ID . ") OR (user_ID = " . $friend->ID . " AND userfriend_FriendID = " . $user->ID . ")";
		$result = $MySQL->query($query);
		return true;
	}
	
	function mmo_user_update_profile_content($user, $content)
	{
		if ($user == null) return false;
		
		$MySQL->query("UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Users SET member_profile_content = '" . $MySQL->real_escape_string($content) . "' WHERE user_ID = " . $user->ID);
		return ($result->errno == 0);
	}
	
?>