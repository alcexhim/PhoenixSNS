<?php
	// www.psychatica.com/community/members/alcexhim/journals/psydev/entries.rss
	$entries = $journal->GetEntries();
	
	switch ($path[4])
	{
		case "entries.rss":
		{
			header("Content-Type: text/xml; charset=UTF-8");
			
			echo("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n");
			echo("<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\r\n");
			echo("    <channel>\r\n");
			echo("        <title>" . $journal->Title . "</title>\r\n");
			echo("        <author>" . $journal->Creator->LongName . "</author>\r\n");
			echo("        <description><![CDATA[" . JH\Utilities::HtmlDecode($journal->Description) . "]]></description>\r\n");
			echo("        <link>" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name, true) . "</link>\r\n");
			echo("        <lastBuildDate>" . date("D, d M Y H:i:s T") . "</lastBuildDate>\r\n");
			if ($journal->CreationDate != null)
			{
				echo("        <pubDate>" . date("D, d M Y H:i:s T", strtotime($journal->CreationDate)) . "</pubDate>\r\n");
			}
			echo("        <ttl>1800</ttl>\r\n");
			
			foreach ($entries as $entry)
			{
				echo("        <item>\r\n");
				echo("            <title>" . $entry->Title . "</title>\r\n");
				echo("            <description><![CDATA[" . JH\Utilities::HtmlDecode($entry->Description) . "]]></description>\r\n");
				echo("            <link>" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $entry->Name, true) . "</link>\r\n");
				echo("            <pubDate>" . date("D, d M Y H:i:s T", strtotime($entry->TimestampCreated)) . "</pubDate>\r\n");
				echo("        </item>\r\n");
			}
			echo("    </channel>\r\n");
			echo("</rss>");
			break;
		}
		case "entries.atom":
		{
			header("Content-Type: text/xml; charset=UTF-8");
			
			echo ("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n");
			echo("<feed xmlns=\"http://www.w3.org/2005/Atom\">\r\n");
			echo("    <title>" . $journal->Title . "</title>\r\n");
			echo("    <subtitle><![CDATA[" . JH\Utilities::HtmlDecode($journal->Description) . "]]></subtitle>\r\n");
			echo("    <link href=\"" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries.atom") . "\" rel=\"self\" />\r\n");
			echo("    <link href=\"" . System::ExpandRelativePath("~/") . "\" />\r\n");
			echo("    <id>urn:uuid:60a76c80-d399-11d9-b91C-0003939e0af6</id>\r\n");
			echo("    <updated>" . date(DateTime::ATOM, strtotime($entry->TimestampCreated)) . "</updated>\r\n");
			
			foreach ($entries as $entry)
			{
				echo("    <entry>\r\n");
				echo("        <title>" . $entry->Title . "</title>\r\n");
				echo("        <link href=\"" . System::ExpandRelativePath("~/community/members/" . $thisuser->ShortName . "/journals/" . $journal->Name . "/entries/" . $entry->Name) . "\" />\r\n");
				
				// echo("        <link rel=\"alternate\" type=\"text/html\" href=\"http://example.org/2003/12/13/atom03.html\" />\r\n");
				// echo("        <link rel=\"edit\" href=\"http://example.org/2003/12/13/atom03/edit\" />
				
				echo("        <id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>\r\n");
				echo("        <updated>" . date(DateTime::ATOM, strtotime($entry->TimestampModified)) . "</updated>\r\n");
				echo("        <summary><![CDATA[" . JH\Utilities::HtmlDecode($entry->Content) . "]]></summary>\r\n");
				echo("        <author>\r\n");
				echo("            <name>" . $journal->Creator->LongName . "</name>\r\n");
				// echo("            <email>johndoe@example.com</email>\r\n");
				echo("        </author>\r\n");
				echo("    </entry>");
			}
			echo("</feed>");
			break;
		}
	}
?>
	