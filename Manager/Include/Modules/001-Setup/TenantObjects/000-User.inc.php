<?php
	use PhoenixSNS\Objects\DataType;
	
	use PhoenixSNS\Objects\MultipleInstanceProperty;
	use PhoenixSNS\Objects\SingleInstanceProperty;
	
	use PhoenixSNS\Objects\Tenant;
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameter;
	use PhoenixSNS\Objects\TenantStringTableEntry;
	
	use PhoenixSNS\Objects\TenantEnumerationChoice;
	
	$enumUserProfileVisibility = $tenant->CreateEnumeration("UserProfileVisibility", "Specifies the possible values for the ProfileVisibility property on the User object.",
	array
	(
		new TenantEnumerationChoice("Everyone", "Your profile is visible to everyone inside and outside the site."),
		new TenantEnumerationChoice("Sitewide", "Your profile is visible only to other registered users."),
		new TenantEnumerationChoice("FriendsExtended", "Your profile is visible to your friends and friends of your friends."),
		new TenantEnumerationChoice("Friends", "Your profile is visible only to you and your friends."),
		new TenantEnumerationChoice("Private", "Your profile is visible only to you.")
	));
	
	$enumUserPresenceStatus = $tenant->CreateEnumeration("UserPresenceStatus", "Specifies the possible values for the ProfileVisibility property on the User object.",
	array
	(
		new TenantEnumerationChoice("Offline", "You are not online."),
		new TenantEnumerationChoice("Available", "You are available for other people to chat with."),
		new TenantEnumerationChoice("Away", "You are away from your computer at the moment."),
		new TenantEnumerationChoice("ExtendedAway", "You are going to be away for an extended period of time."),
		new TenantEnumerationChoice("Busy", "You are busy and do not want to be bothered."),
		new TenantEnumerationChoice("Hidden", "Your presence status is hidden.")
	));
	
	$object = $tenant->CreateObject("User",
	array
	(
		new TenantStringTableEntry($langEnglish, "User")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "Contains information about a PhoenixSNS user account.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("LoginID", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("URL", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("DisplayName", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("EmailAddress", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("EmailConfirmationCode", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("BirthDate", DataType::GetByName("DateTime")),
		new TenantObjectInstanceProperty("RealName", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("PasswordHash", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("PasswordSalt", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("Theme", DataType::GetByName("SingleInstance")),
		new TenantObjectInstanceProperty("Language", DataType::GetByName("SingleInstance")),
		new TenantObjectInstanceProperty("ProfileVisibility", DataType::GetByName("SingleInstance")),
		new TenantObjectInstanceProperty("ConsecutiveLoginCount", DataType::GetByName("Number")),
		new TenantObjectInstanceProperty("ConsecutiveLoginFailures", DataType::GetByName("Number")),
		new TenantObjectInstanceProperty("LastLoginTimestamp", DataType::GetByName("DateTime")),
		new TenantObjectInstanceProperty("PresenceStatus", DataType::GetByName("SingleInstance")),
		new TenantObjectInstanceProperty("PresenceMessage", DataType::GetByName("Text")),
		new TenantObjectInstanceProperty("RegistrationTimestamp", DataType::GetByName("DateTime")),
		new TenantObjectInstanceProperty("RegistrationIPAddress", DataType::GetByName("IPAddress")),
		new TenantObjectInstanceProperty("StartPage", DataType::GetByName("SingleInstance"))
	));
	
	$object->CreateMethod("SaltPassword", array(),
	
	// code goes here... you cannot "use" namespaces here; please put them in NamespaceReferences!!!
<<<'EOD'
return \UUID::Generate();
EOD
, "Generates a PhoenixSNS password salt using a Universally Unique Identifier (UUID).");
	
	$object->CreateMethod("HashPassword", array
	(
		new TenantObjectMethodParameter("input")
	),
	
	// code goes here... you cannot "use" namespaces here; please put them in NamespaceReferences!!!
<<<'EOD'
return hash("sha512", $input);
EOD
, "Generates a PhoenixSNS password hash using the SHA-512 algorithm.");
	
	$object->CreateMethod("ValidateCredentials", array
	(
		new TenantObjectMethodParameter("username"),
		new TenantObjectMethodParameter("password")
	),
	
	// code goes here... you cannot "use" namespaces here; please put them in NamespaceReferences!!!
<<<'EOD'
$tenant = Tenant::GetCurrent();
$inst = $thisObject->GetInstance(array
(
	new TenantQueryParameter("LoginID", $username)
));

// if there is no user with this LoginID, return null
if ($inst == null) return null;

// get the password salt used in the creation of this instance
$salt = $inst->GetPropertyValue($thisObject->GetInstanceProperty("PasswordSalt"));

// generate the salted password hash by concatenating the salt and the password
$pwhash = hash("sha512", $salt . $password);

// try to get an instance with the specified login ID and password hash
$user = $thisObject->GetInstance(array
(
	new TenantQueryParameter("LoginID", $username),
	new TenantQueryParameter("PasswordHash", $pwhash)
));

return $user;
EOD
, "Validates the given user name and password against the database and returns an instance of the User if the validation is successful.", array
(
	'PhoenixSNS\Objects\Tenant',
	'PhoenixSNS\Objects\TenantObjectMethodParameterValue',
	'PhoenixSNS\Objects\TenantQueryParameter'
));
	
	$object->CreateMethod("GetCurrentUser", array(),
	
	// code goes here... you cannot "use" namespaces here; please put them in NamespaceReferences!!!
<<<'EOD'
$tenant = Tenant::GetCurrent();
if (!((isset($_SESSION["CurrentUserName[" . $tenant->ID . "]"])) && (isset($_SESSION["CurrentPassword[" . $tenant->ID . "]"]))))
{
	return null;
}

$username = $_SESSION["CurrentUserName[" . $tenant->ID . "]"];
$password = $_SESSION["CurrentPassword[" . $tenant->ID . "]"];

$inst = $thisObject->GetInstance(array
(
	new TenantQueryParameter("LoginID", $username)
));

// if there is no user with this LoginID, return null
if ($inst == null) return null;

// get the password salt used in the creation of this instance
$salt = $inst->GetPropertyValue($thisObject->GetInstanceProperty("PasswordSalt"));

// generate the salted password hash by concatenating the salt and the password
$pwhash = hash("sha512", $salt . $password);

// try to get an instance with the specified login ID and password hash
$user = $thisObject->GetInstance(array
(
	new TenantQueryParameter("LoginID", $username),
	new TenantQueryParameter("PasswordHash", $pwhash)
));

return $user;
EOD
, "Gets the user that is currently logged into PhoenixSNS.", array
(
	// using statements go here
	'PhoenixSNS\Objects\Tenant',
	'PhoenixSNS\Objects\TenantQueryParameter'
));
	
?>