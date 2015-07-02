var PresenceType =
{
	"Offline": 0,
	"Active": 1,
	"Composing": 2,
	"Paused": 3
};

// Use this code to test:
// javascript:

function XMPPUserInfo(username, domain, resource)
{
	this.UserName = username;
	this.Domain = domain;
	this.Resource = resource;
	
	this.ToString = function()
	{
		return this.UserName + "@" + this.Domain + "/" + this.Resource;
	};
}
XMPPUserInfo.Parse = function(jid)
{
	// testing@example.com/resource
	
	var username = jid;
	var domain = "";
	var resource = "";
	if (username.indexOf('/') != -1)
	{
		resource = username.substr(username.indexOf('/') + 1); 
		username = username.substr(0, username.indexOf('/'));
	}
	if (username.indexOf('@') != -1)
	{
		domain = username.substr(username.indexOf('@') + 1);
		username = username.substr(0, username.indexOf('@'));
	}
	return new XMPPUserInfo(username, domain, resource);
};

function XMPPClient(username, password)
{
	this.ServerName = "psychatica.com";
	this.ResourceName = "Psychatica-XMPP-1";
	this.UserName = username;
	
	this.PresenceUpdated = new Callback(this);
	this.MessageReceived = new Callback(this);
	this.UserDisconnected = new Callback(this);
	
	this.OnConnect = function(client, statusCode, errorCondition)
	{
		if (statusCode == 5)
		{
			client.NotifyAvailable(true);
		}
	};
	this.OnChatMessageReceived = function(message, sender)
	{
		console.info("received a message delivery request from " + sender + ": \"" + message + "\"");
	};
	this.OnMessageReceived = function(client, stanza)
	{
		// <message type='chat' id='purplefcc22ab8' to='testacct@psychatica.com/12266597981372447628512056'>
		//		<active xmlns='http://jabber.org/protocol/chatstates'/>
		// 		<body>hey zippy</body>
		// </message>
		if (stanza.tagName == "message")
		{
			var bodyReceived = false;
			var htmlReceived = false;
			var message = "";
			
			var sender = stanza.attributes["from"].value;
			var userinfo = XMPPUserInfo.Parse(sender);
			
			for (var i = 0; i < stanza.childNodes.length; i++)
			{
				if (stanza.childNodes[i].tagName == "html")
				{
					if (htmlReceived) continue;
					
					var obj = stanza.childNodes[i];
					message = obj.innerHTML; // JH.Utilities.HtmlDecode(obj.innerHTML);
					client.MessageReceived.Execute
					(
						{
							"User": userinfo,
							"Content": message,
							"ContentType": "text/html"
						}
					);
					
					htmlReceived = true;
				}
				else if (stanza.childNodes[i].tagName == "body")
				{
					if (bodyReceived) continue;
					
					var obj = stanza.childNodes[i];
					message = JH.Utilities.HtmlDecode(obj.innerHTML);
					client.MessageReceived.Execute
					(
						{
							"User": userinfo,
							"Content": message,
							"ContentType": "text/plain"
						}
					);
					
					bodyReceived = true;
				}
				else if (stanza.childNodes[i].tagName == "active")
				{
					client.PresenceUpdated.Execute
					(
						{
							"User": userinfo,
							"PresenceType": PresenceType.Active
						}
					);
				}
				else if (stanza.childNodes[i].tagName == "composing")
				{
					client.PresenceUpdated.Execute
					(
						{
							"User": userinfo,
							"PresenceType": PresenceType.Composing
						}
					);
				}
				else if (stanza.childNodes[i].tagName == "paused")
				{
					client.PresenceUpdated.Execute
					(
						{
							"User": userinfo,
							"PresenceType": PresenceType.Paused
						}
					);
				}
				else
				{
					console.info("received an unknown MESSAGE stanza, dir follows");
					console.dir(stanza);
				}
			}
			
			if (htmlReceived || bodyReceived)
			{
				client.OnChatMessageReceived(message, sender);
			}
		}
		else if (stanza.tagName == "iq")
		{
			var receiver = stanza.getAttribute("to").value;
			var type = stanza.getAttribute("type").value;
			var elem = stanza.childNodes[0];
			console.info("received a \"" + elem.tagName + "\" iq stanza from \"" + username + "\"");
			
			if (type == "get")
			{
				switch (elem.tagName)
				{
					case "vCard":
					{
						var fullname = "Kristina Lister";
						var iq = $iq({ "from": receiver, "type": "result" });
						iq.c("vCard", { "xmlns": "vcard-temp" });
						iq.t("FN", fullname);
						iq.up();
						iq.up();
						
						console.info("sending out IQ");
						console.info(iq.toString());
						break;
					}
				}
			}
			else if (type == "result")
			{
				switch (elem.tagName)
				{
					case "bind":
					{
						var elem1 = elem.childNodes[0];
						switch (elem1.tagName)
						{
							case "jid":
							{
								alert(elem1.innerHTML + " connected");
								break;
							}
						}
						break;
					}
				}
			}
		}
		else
		{
			console.info("received an unknown stanza, dir follows");
			console.dir(stanza);
		}
		return true;
	};
	this.OnPresenceReceived = function(client, stanza)
	{
		// <message type='chat' id='purplefcc22ab8' to='testacct@psychatica.com/12266597981372447628512056'>
		//		<active xmlns='http://jabber.org/protocol/chatstates'/>
		// 		<body>hey zippy</body>
		// </message>
		// var type = stanza.getAttribute("type").value;
		var userinfo = null;
		if (stanza.attributes["from"] != null) userinfo = XMPPUserInfo.Parse(stanza.attributes["from"].value);
		
		var type = null;
		if (stanza.attributes["type"] != null) type = stanza.attributes["type"].value;
		
		switch (type)
		{
			case "subscribe":
			{
				var username = stanza.getAttribute("from").value;
				if (stanza.getAttribute("type").value == "subscribe" /* && is_friend(username) */)
				{
					// Send a 'subscribed' notification back to accept the incoming
					// subscription request
					client.strophe.send($pres({ "to": username, "type": "subscribed" }));
				}
				
				console.info("received a stanza, dir follows");
				console.dir(stanza);
				break;
			}
			case "unavailable":
			{
				client.UserDisconnected.Execute
				(
					{
						"User": userinfo
					}
				);
				break;
			}
			default:
			{
				console.log("undefined presence type '" + type + "', dir follows");
				console.dir(stanza);
				/*
				if (stanza.childNodes[i].tagName == "active")
				{
					client.PresenceUpdated.Execute
					(
						{
							"User": userinfo,
							"PresenceType": PresenceType.Active
						}
					);
				}
				else if (stanza.childNodes[i].tagName == "composing")
				{
					client.PresenceUpdated.Execute
					(
						{
							"User": userinfo,
							"PresenceType": PresenceType.Composing
						}
					);
				}
				else if (stanza.childNodes[i].tagName == "paused")
				{
					client.PresenceUpdated.Execute
					(
						{
							"User": userinfo,
							"PresenceType": PresenceType.Paused
						}
					);
				}
				*/
				break;
			}
		}
		return true;
	};
	
	this.NotifyAvailable = function(available, receiver)
	{
		var sender = this.UserName + "@" + this.ServerName;
		switch (available)
		{
			case false:
			{
				var tagPresence = $pres({ "from": sender /*, "to": receiver */, "type": "unavailable" });
				
				console.info("presence changed; sending message to the XMPP server");
				console.info(tagPresence.toString());
				
				this.strophe.send(tagPresence);
				break;
			}
			case true:
			{
				var tagPresence = $pres({ "from": sender });
				console.info("presence changed; sending message to the XMPP server");
				console.info(tagPresence.toString());
				
				window.setTimeout(function(client) { client.strophe.send(tagPresence); }, 50, this);
				break;
			}
		}
	};
	this.NotifyPresenceChange = function(presenceType, receiver)
	{
		var sender = this.UserName + "@" + this.ServerName;
		if (!receiver) receiver = this.UserName; // + "/" + this.ResourceName;
		receiver += "@" + this.ServerName;
		
		switch (presenceType)
		{
			case PresenceType.Active:
			{
				var tagMessage = $msg({ "to": receiver, "type": "chat" });
				var tagComposing = tagMessage.c("active", { "xmlns": "http://jabber.org/protocol/chatstates" });
				tagMessage = tagComposing.up();
				
				console.info("presence changed; sending message to the XMPP server");
				console.info(tagMessage.toString());
				
				this.strophe.send(tagMessage);
				break;
			}
			case PresenceType.Composing:
			{
				var tagMessage = $msg({ "to": receiver, "type": "chat" });
				var tagComposing = tagMessage.c("composing", { "xmlns": "http://jabber.org/protocol/chatstates" });
				tagMessage = tagComposing.up();
				
				console.info("presence changed; sending message to the XMPP server");
				console.info(tagMessage.toString());
				
				this.strophe.send(tagMessage);
				break;
			}
			case PresenceType.Paused:
			{
				var tagMessage = $msg({ "to": receiver, "type": "chat" });
				var tagComposing = tagMessage.c("paused", { "xmlns": "http://jabber.org/protocol/chatstates" });
				tagMessage = tagComposing.up();
				
				console.info("presence changed; sending message to the XMPP server");
				console.info(tagMessage.toString());
				
				this.strophe.send(tagMessage);
				break;
			}
		}
	};
	
	this.SendMessage = function(username, message)
	{
		var sender = this.UserName + "@" + this.ServerName; // + "/" + this.ResourceName;
		var receiver = username + "@" + this.ServerName; // + "/" + this.ResourceName;
		
		var tagMessage = $msg({ "from": sender, "to": receiver, "type": "chat" });
		var tagBody = tagMessage.c("body");
		tagBody.t(message);
		tagMessage = tagBody.up();
		
		var tagHTML = tagMessage.c("html", { "xmlns": "http://jabber.org/protocol/xhtml-im" });
		tagBody = tagHTML.c("body", { "xmlns": "http://www.w3.org/1999/xhtml" });
		tagBody.t(message);
		tagHTML = tagBody.up();
		tagMessage = tagHTML.up();
				
		console.info("chat message sent; sending message to the XMPP server");
		console.info(tagMessage.toString());
		
		window.setTimeout(function(client) { client.strophe.send(tagMessage) }, 50, this);
		
		/*
			<message from='alcexhim@psychatica.com/29787588431372426289776875' to='admin@psychatica.com/24355117361372426332211547' type='chat' id='purplef5b34fb2'>
				<active xmlns='http://jabber.org/protocol/chatstates'/>
				<body>hey</body>
			</message>
			<message from='alcexhim@psychatica.com/29787588431372426289776875' to='admin@psychatica.com/24355117361372426332211547' type='chat' id='purplef5b34fb3'>
				<active xmlns='http://jabber.org/protocol/chatstates'/>
			</message>
			<message from='alcexhim@psychatica.com/29787588431372426289776875' to='admin@psychatica.com/24355117361372426332211547' type='chat' id='purplef5b34fb3'>
				<paused xmlns='http://jabber.org/protocol/chatstates'/>
			</message>
		*/
	};
	
	this.strophe = new Strophe.Connection("http://www.psychatica.com:5280/http-bind");
	this.strophe.addHandler(this.OnMessageReceived.PrependArgument(this), null, "iq", "get", null, null, null);
	this.strophe.addHandler(this.OnMessageReceived.PrependArgument(this), null, "message", "chat", null, null, null);
	this.strophe.addHandler(this.OnPresenceReceived.PrependArgument(this), null, "presence", "subscribe", null, null, null);
	this.strophe.addHandler(this.OnPresenceReceived.PrependArgument(this), null, "presence", "unavailable", null, null, null);
	this.strophe.addHandler(this.OnPresenceReceived.PrependArgument(this), null, "presence", "", null, null, null);
	this.strophe.connect(this.UserName + "@" + this.ServerName, password, this.OnConnect.PrependArgument(this));
	
	// window.setTimeout(function(client) { client.NotifyPresenceChange(1); }, 1000, this);
}