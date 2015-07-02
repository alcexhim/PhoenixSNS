<?php
	use PhoenixSNS\Objects\DataType;
	
	use PhoenixSNS\Objects\MultipleInstanceProperty;
	use PhoenixSNS\Objects\SingleInstanceProperty;
	
	use PhoenixSNS\Objects\TenantObjectProperty;
	use PhoenixSNS\Objects\TenantObjectInstanceProperty;
	use PhoenixSNS\Objects\TenantObjectInstancePropertyValue;
	use PhoenixSNS\Objects\TenantObjectMethodParameter;
	
	use PhoenixSNS\Objects\TenantEnumerationChoice;
	use PhoenixSNS\Objects\TenantStringTableEntry;
	
	$object = $tenant->CreateObject("Post",
	array
	(
		new TenantStringTableEntry($langEnglish, "Post")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "Represents any content posted by a user. This includes official content that appears on the front page of the site if your tenant is configured to show official posts.")
	),
	array
	(
		// property name, data type, default value, value required?, enumeration, require choice from enumeration?
		new TenantObjectInstanceProperty("Author", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($tenant->GetObject("User")))),
		new TenantObjectInstanceProperty("Title"),
		new TenantObjectInstanceProperty("Content"),
		new TenantObjectInstanceProperty("PostType"), // single instance of a PostType object
		new TenantObjectInstanceProperty("CreationDate") // DateTime
	));
	
	$object->CreateMethod("SetFeedback",
	array
	(
	),
	
	// code goes here... you cannot "use" namespaces here; please put them in NamespaceReferences!!!
<<<'EOD'
echo('Post::SetFeedback - not implemented');
die();
EOD
, "Sets the personal feedback for the current user with respect to this post. Specify the feedback type (i.e. like, dislike, etc.) and optionally a short comment. The post will display the number of users with that type of feedback. You can configure how many feedback options are displayed. If there are more feedback options than this limit, they will appear as a drop-down menu alongside the primary option if the limit is 1, or as a separate 'More' option if the limit is greater than 1.");
	
	$objUserDashboardPost = $tenant->CreateObject("MemberMainPageDashboardPost",
	array
	(
		new TenantStringTableEntry($langEnglish, "Member Main Page Dashboard Post")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A post on the Dashboard for all members.")
	), array
	(
	), $tenant->GetObject("Post"));
	
	$objUserDashboardPost = $tenant->CreateObject("GuestMainPageDashboardPost",
	array
	(
		new TenantStringTableEntry($langEnglish, "Guest Main Page Dashboard Post")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A post on the Dashboard for people who are not logged in.")
	),
	array
	(
	), $tenant->GetObject("Post"));
	
	$objUserDashboardPost = $tenant->CreateObject("UserDashboardPost",
	array
	(
		new TenantStringTableEntry($langEnglish, "User Dashboard Post")
	),
	array
	(
		new TenantStringTableEntry($langEnglish, "A post on a user's Dashboard. The post will be visible on the dashboards of all the users in the Receivers.")
	),
	array
	(
		new TenantObjectInstanceProperty("Receivers", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(array(), array($tenant->GetObject("User"))))
	), $tenant->GetObject("Post"));
?>