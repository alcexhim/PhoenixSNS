<?php
	namespace PhoenixSNS\Objects;
	
	use WebFX\System;
	use \PDO;
	
	/**
	 * Provides methods to generate a random string using a specified character set.
	 * @author Michael Becker
	 */
	class RandomStringGenerator
	{
		/**
		 * Generates a random string of the specified length using the specified character set.
		 * @param string $valid_chars The character set to use.
		 * @param int $length The length of the string to generate.
		 * @return string A random string.
		 */
		public static function Generate($valid_chars, $length)
		{
			// start with an empty random string
			$random_string = "";

			// count the number of chars in the valid chars string so we know how many choices we have
			$num_valid_chars = strlen($valid_chars);

			// repeat the steps until we've created a string of the right length
			for ($i = 0; $i < $length; $i++)
			{
				// pick a random number from 1 up to the number of valid chars
				$random_pick = mt_rand(1, $num_valid_chars);

				// take the random character out of the string of valid chars
				// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
				$random_char = $valid_chars[$random_pick-1];

				// add the randomly-chosen char onto the end of our string so far
				$random_string .= $random_char;
			}

			// return our finished random string
			return $random_string;
		}
	}
	
	/**
	 * Provides an ObjectFX user that can access the Tenant Manager
	 * @author Michael Becker
	 */
	class User
	{
		/**
		 * The unique, incremental ID number of this User
		 * @var int
		 */
		public $ID;
		/**
		 * The user name for signing into the system
		 * @var string
		 */
		public $UserName;
		/**
		 * The password for signing into the system (used only on an update operation)
		 * @var string
		 */
		public $Password;
		/**
		 * The salt value for securely hashing the password
		 * @var string
		 */
		public $PasswordSalt;
		/**
		 * The name displayed for this user around the site
		 * @var string
		 */
		public $DisplayName;
		
		/**
		 * True if the user's account is locked; false otherwise.
		 * @var boolean
		 */
		public $AccountLocked;
		/**
		 * True if the user must change his or her password upon login; false otherwise.
		 * @var boolean
		 */
		public $ForcePasswordChange;
		
		/**
		 * Counts all of the available Users.
		 * @return int The number of Users available on this server.
		 */
		public static function Count()
		{
			global $MySQL;
			$query = "SELECT COUNT(user_ID) FROM " . System::$Configuration["Database.TablePrefix"] . "Users";
			$result = $MySQL->query($query);
			if ($result === false) return 0;
			$values = $result->fetch_array();
			return $values[0];
		}
		/**
		 * Creates a new User object based on the given values from the database.
		 * @param array $values 
		 * @return \PhoenixSNS\Objects\User based on the values of the fields in the given associative array
		 */
		public static function GetByAssoc($values)
		{
			$item = new User();
			$item->ID = $values["user_ID"];
			$item->UserName = $values["user_LoginID"];
			$item->DisplayName = $values["user_DisplayName"];
			$item->AccountLocked = ($values["user_AccountLocked"] != 0);
			$item->ForcePasswordChange = ($values["user_ForcePasswordChange"] != 0);
			return $item;
		}
		/**
		 * Retrieves all Users
		 * @param string $max The maximum number of Users to return
		 * @return \PhoenixSNS\Objects\User[] array of all Users on the server
		 */
		public static function Get($max = null)
		{
			global $MySQL;
			$query = "SELECT * FROM ". System::$Configuration["Database.TablePrefix"] . "Users";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$result = $MySQL->query($query);
			
			$retval = array();
			if ($result === false) return $retval;
			
			for ($i = 0; $i < $result->num_rows; $i++)
			{
				$values = $result->fetch_assoc();
				$retval[] = User::GetByAssoc($values);
			}
			return $retval;
		}
		/**
		 * Retrieves a single User with the given ID.
		 * @param int $id The ID of the User to return
		 * @return NULL|\PhoenixSNS\Objects\User The User with the given ID, or null if no User with the given ID was found or the given ID was invalid
		 */
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM ". System::$Configuration["Database.TablePrefix"] . "Users WHERE user_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$values = $result->fetch_assoc();
			return User::GetByAssoc($values);
		}
		/**
		 * Retrieves a single User with the given credentials.
		 * @param string $username The user name of the User to search for.
		 * @param string $password The password of the User to search for.
		 * @return NULL|\PhoenixSNS\Objects\User The User with the given credentials, or null if no User with the given credentials was found or the credentials were invalid
		 */
		public static function GetByCredentials($username, $password)
		{
			global $pdo;
			
			$query = $pdo->prepare("SELECT * FROM ". System::$Configuration["Database.TablePrefix"] . "Users WHERE user_LoginID = :LoginID");
			$query->bindParam(":LoginID", $username);
			$result = $query->execute();
			if ($result === false) return null;
			
			$values = $query->fetch(PDO::FETCH_ASSOC);
			
			$passwordSalt = $values["user_PasswordSalt"];
			$passwordHashCmp = $values["user_PasswordHash"];
			
			$passwordHash = hash("sha512", $password . $passwordSalt);
			if ($passwordHash == $passwordHashCmp) return User::GetByAssoc($values);
			
			return null;
		}
		/**
		 * Retrieves the currently logged in user, or NULL if no user is currently logged in.
		 * @return \PhoenixSNS\Objects\User|NULL The currently logged in user, or NULL if no user is currently logged in.
		 */
		public static function GetCurrent()
		{
			if (isset($_SESSION["Authentication.UserName"]) && isset($_SESSION["Authentication.Password"]))
			{
				return User::GetByCredentials($_SESSION["Authentication.UserName"], $_SESSION["Authentication.Password"]);
			}
			return null;
		}
		/**
		 * Creates a User on the server with the given parameters.
		 * @param string $username The user name of the user to create.
		 * @param string $password The password of the user to create.
		 * @return \PhoenixSNS\Objects\User|NULL The newly-created User, or NULL if the creation operation failed.
		 */
		public static function Create($username, $password)
		{
			$item = new User();
			$item->UserName = $username;
			$item->Password = $password;
			
			if ($item->Update())
			{
				return $item;
			}
			return null;
		}
		/**
		 * Updates the server with the information in this object.
		 * @param boolean $changePassword True if the user's password should be changed; false otherwise.
		 * @param string $previousPassword The previous password of the user whose password should be changed.
		 * @return boolean True if the update succeeded; false if an error occurred.
		 */
		public function Update($changePassword = false, $previousPassword = null)
		{
			global $MySQL;
			
			$passwordSalt = RandomStringGenerator::Generate("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*(),./;'[]\\\"-=_+{}|:<>?~`", 12);
			$passwordHash = hash("sha512", $this->Password . $passwordSalt);
			
			if ($this->ID != null)
			{
				// #region Determine if previous password matches new password
				if ($changePassword)
				{
					$query = "SELECT * FROM ". System::$Configuration["Database.TablePrefix"] . "Users WHERE user_LoginID = '" . $MySQL->real_escape_string($username) . "'";
					$result = $MySQL->query($query);
					if ($result === false) return false;
					
					$values = $result->fetch_assoc();
					$passwordHashOld = $values["user_PasswordHash"];
					$passwordSaltOld = $values["user_PasswordSalt"];
					
					if ($passwordHashOld != "")
					{
						$passwordHashNew = hash("sha512", $previousPassword . $passwordSaltOld);
						if ($passwordHashOld != $passwordHashNew)
						{
							return false;
						}
					}
				}

				$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "Users SET ";
				$query .= "user_LoginID = '" . $MySQL->real_escape_string($this->UserName) . "', ";
				$query .= "user_DisplayName = '" . $MySQL->real_escape_string($this->DisplayName) . "', ";
				$query .= "user_AccountLocked = " . ($this->AccountLocked ? "1" : "0") . ", ";
				$query .= "user_ForcePasswordChange = " . ($this->ForcePasswordChange ? "1" : "0");
				if ($changePassword)
				{
					$query .= ", ";
					$query .= "user_PasswordHash = '" . $MySQL->real_escape_string($passwordHash) . "', ";
					$query .= "user_PasswordSalt = '" . $MySQL->real_escape_string($passwordSalt) . "'";
				}
				$query .= " WHERE user_ID = " . $this->ID;
			}
			else
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "Users (user_LoginID, user_DisplayName, user_PasswordHash, user_PasswordSalt, user_AccountLocked, user_ForcePasswordChange, user_TimestampCreated) VALUES (";
				$query .= "'" . $MySQL->real_escape_string($this->UserName) . "', ";
				$query .= "'" . $MySQL->real_escape_string($this->DisplayName) . "', ";
				$query .= "'" . $MySQL->real_escape_string($passwordHash) . "', ";
				$query .= "'" . $MySQL->real_escape_string($passwordSalt) . "', ";
				$query .= ($this->AccountLocked ? "1" : "0") . ", ";
				$query .= ($this->ForcePasswordChange ? "1" : "0") . ", ";
				$query .= "NOW()";
				$query .= ")";
			}
			
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			if ($this->ID == null)
			{
				$this->ID = $MySQL->insert_id;
			}
			return true;
		}
		
	}
?>