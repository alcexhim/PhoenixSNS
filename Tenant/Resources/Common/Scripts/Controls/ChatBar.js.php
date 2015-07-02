<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../../../..";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	header("Content-Type: text/javascript");
?>

function ChatBar(id, userName)
{
	this.ID = id;
	this.UserName = userName;
	this.DisplayName = userName;
	this.CreateChat = function(userName, displayName, imageURL)
	{
		imageURL = System.ExpandRelativePath(imageURL);
		var panels = document.getElementById("ChatArea_" + this.ID + "_Panels");
		var cid = panels.childNodes.length + 1;
		
		var ChatPanel = document.createElement("div");
		ChatPanel.className = "ChatPanel";
		ChatPanel.id = "ChatArea_" + this.ID + "_Panel_" + cid;
		
		var ChatPanelReceiver = document.createElement("input");
		ChatPanelReceiver.setAttribute("type", "hidden");
		ChatPanelReceiver.id = "ChatArea_" + this.ID + "_Panel_" + cid + "_Receiver";
		ChatPanelReceiver.value = userName;
		ChatPanel.appendChild(ChatPanelReceiver);
		
		var ChatPanelReceiverDisplayName = document.createElement("input");
		ChatPanelReceiverDisplayName.setAttribute("type", "hidden");
		ChatPanelReceiverDisplayName.id = "ChatArea_" + this.ID + "_Panel_" + cid + "_Receiver_DisplayName";
		ChatPanelReceiverDisplayName.value = displayName;
		ChatPanel.appendChild(ChatPanelReceiverDisplayName);
		
		var ChatPanelReceiverID = document.createElement("input");
		ChatPanelReceiverID.setAttribute("type", "hidden");
		ChatPanelReceiverID.id = "ChatArea_" + this.ID + "_Panel_" + cid + "_Receiver_" + userName + "_ID";
		ChatPanelReceiverID.value = cid;
		ChatPanel.appendChild(ChatPanelReceiverID);
		
		var ChatPanelTitle = document.createElement("div");
		ChatPanelTitle.className = "ChatPanelTitle";
		
		var aTitle = document.createElement("a");
		aTitle.className = "Title";
		aTitle.href = "#";
		aTitle.addEventListener("click", function(e)
		{
			this.ActivateChat(cid);
			return false;
		});
		aTitle.innerHTML = displayName;
		ChatPanelTitle.appendChild(aTitle);
		
		var aPopout = document.createElement("a");
		aPopout.className = "Close";
		aPopout.setAttribute("target", "_blank");
		aPopout.href = System.ExpandRelativePath("~/account/messages/" + userName);
		aPopout.addEventListener("click", function(e)
		{
			this.ActivateChat(cid);
			return false;
		});
		
		var lblPopout = document.createElement("i");
		lblPopout.className = "fa fa-external-link";
		aPopout.appendChild(lblPopout);
		
		ChatPanelTitle.appendChild(aPopout);
		
		var aClose = document.createElement("a");
		aClose.className = "Close";
		aClose.href = "#";
		aClose.addEventListener("click", function(e)
		{
			this.CloseChat(cid);
			return false;
		});
		
		var lblClose = document.createElement("i");
		lblClose.className = "fa fa-times";
		aClose.appendChild(lblClose);
		
		ChatPanelTitle.appendChild(aClose);
		
		ChatPanel.appendChild(ChatPanelTitle);
		
		var ChatPanelHistory = document.createElement("div");
		ChatPanelHistory.className = "ChatPanelHistory";
		ChatPanelHistory.id = "ChatArea_" + this.ID + "_Panel_" + cid + "_History";
		ChatPanel.appendChild(ChatPanelHistory);
		
		var ChatPanelStatus = document.createElement("div");
		ChatPanelStatus.className = "ChatPanelStatus";
		ChatPanelStatus.id = "ChatArea_" + this.ID + "_Panel_" + cid + "_Status";
		ChatPanel.appendChild(ChatPanelStatus);
		
		var ChatPanelInput = document.createElement("textarea");
		ChatPanelInput.className = "ChatPanelInput";
		ChatPanelInput.id = "ChatArea_" + this.ID + "_Panel_" + cid + "_Input";
		ChatPanelInput.addEventListener("keydown", function(e)
		{
			this.OnKeyDown(cid, e);
		});
		ChatPanelInput.addEventListener("keyup", function(e)
		{
			this.OnKeyUp(cid, e);
		});
		ChatPanel.appendChild(ChatPanelInput);
		
		panels.appendChild(ChatPanel);
		
		var buttons = document.getElementById("ChatArea_" + this.ID + "_Buttons");
		var buttonHTML = "<a onclick=\"" + this.ID + ".ActivateChat(" + cid + ");\" id=\"ChatArea_" + this.ID + "_Buttons_" + cid + "_Button\" class=\"ChatBarButton\" href=\"#\"><img src=\"" + imageURL + "\" class=\"ChatBarButtonAvatar\"><span class=\"ChatBarButtonText\">" + displayName + "</span></a>";
		buttons.innerHTML += buttonHTML;
	};
	this.ActivateBuddyList = function()
	{
		var panel = document.getElementById("ChatArea_" + this.ID + "_BuddyListPanel");
		var button = document.getElementById("ChatArea_" + this.ID + "_BuddyListButton");
		
		if (button.className == "ChatBarButton Selected")
		{
			button.className = "ChatBarButton";
			panel.style.display = "none";
		}
		else if (button.className == "ChatBarButton" || button.className == "ChatBarButton Attention")
		{
			button.className = "ChatBarButton Selected";
			
			panel.style.left = button.offsetLeft + "px";
			panel.style.display = "block";
		}
	};
	this.ActivateChat = function(chat_id)
	{
		var chatbar = document.getElementById("ChatArea_" + this.ID + "_Buttons");
		
		var panels = document.getElementById("ChatArea_" + this.ID + "_Panels");
		for (var i = 0; i < panels.childNodes.length; i++)
		{
			if (panels.childNodes[i].tagName == "DIV")
			{
				panels.childNodes[i].style.display = "none";
			}
		}
		
		var panel = document.getElementById("ChatArea_" + this.ID + "_Panel_" + chat_id);
		var button = document.getElementById("ChatArea_" + this.ID + "_Buttons_" + chat_id + "_Button");
		
		if (button.className == "ChatBarButton Selected")
		{
			button.className = "ChatBarButton";
			panel.style.display = "none";
		}
		else if (button.className == "ChatBarButton" || button.className == "ChatBarButton Attention")
		{
			for (var i = 0; i < chatbar.childNodes.length; i++)
			{
				if (chatbar.childNodes[i].tagName == "A" && chatbar.childNodes[i].className == "ChatBarButton Selected")
				{
					chatbar.childNodes[i].className = "ChatBarButton";
				}
			}
			button.className = "ChatBarButton Selected";
			
			panel.style.left = button.offsetLeft + "px";
			panel.style.display = "block";
		}
	};
	this.CloseChat = function(chat_id)
	{
		var panel = document.getElementById("ChatArea_" + this.ID + "_Panel_" + chat_id);
		var button = document.getElementById("ChatArea_" + this.ID + "_Buttons_" + chat_id + "_Button");
		panel.style.display = "none";
		button.style.display = "none";
	};
	this.UpdatePresence = function(id, presence)
	{
		var receiver = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Receiver");
		var receiver_name = receiver.value;
		var status = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Status");
		
		var button = document.getElementById("ChatArea_" + this.ID + "_Buttons_" + id + "_Button");
		
		var cssClass = button.cssClass;
		switch (button.cssClass)
		{
			case "ChatBarButton":
			case "ChatBarButton Active":
			case "ChatBarButton Composing":
			case "ChatBarButton Paused":
			case "ChatBarButton Offline":
			{
				cssClass = "ChatBarButton";
				break;
			}
			case "ChatBarButton Selected":
			case "ChatBarButton Selected Active":
			case "ChatBarButton Selected Composing":
			case "ChatBarButton Selected Paused":
			case "ChatBarButton Selected Offline":
			{
				cssClass = "ChatBarButton Selected";
				break;
			}
			case "ChatBarButton Attention":
			case "ChatBarButton Attention Active":
			case "ChatBarButton Attention Composing":
			case "ChatBarButton Attention Paused":
			case "ChatBarButton Attention Offline":
			{
				cssClass = "ChatBarButton Attention";
				break;
			}
		}
		
		switch (presence)
		{
			case PresenceType.Offline:
			{
				status.innerHTML = receiver_name + " is offline.";
				button.cssClass = cssClass + " Offline";
				break;
			}
			case PresenceType.Active:
			{
				status.innerHTML = "&nbsp;";
				button.cssClass = cssClass + " Active";
				break;
			}
			case PresenceType.Composing:
			{
				status.innerHTML = receiver_name + " is typing...";
				button.cssClass = cssClass + " Composing";
				break;
			}
			case PresenceType.Paused:
			{
				status.innerHTML = receiver_name + " has stopped typing";
				button.cssClass = cssClass + " Paused";
				break;
			}
		}
	};
	this.SendPresence = function(presence, sender)
	{
		xmpp.NotifyPresenceChange(presence, sender);
	};
	this.SendMessage = function(id)
	{
		var receiver = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Receiver");
		var receiver_name = receiver.value;
		var input = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Input");
		var message = input.value;
		
		try
		{
			xmpp.SendMessage(receiver_name, message);
		}
		catch (ex)
		{
			console.info(receiver_name + " is offline or the XMPP server could not be accessed");
		}
		
		input.value = "";
		
		var history = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_History");
		var html = "<div class=\"ChatPanelHistoryMessage\"><img title=\"" + this.DisplayName + "\" alt=\"" + this.DisplayName + "\" src=\"<?php echo(System::ExpandRelativePath("~/community/members/")); ?>" + this.UserName + "/images/avatar/thumbnail.png\" class=\"ChatPanelHistoryMessageAvatar\"><div class=\"ChatPanelHistoryMessageContent\">" + JH.Utilities.HtmlEncode(message) + "</div></div>";
		history.innerHTML += html;
		history.scrollTop = history.scrollTopMax;
	};
	this.GetDisplayNameForUserName = function(username)
	{
		return username;
	};
	this.GetChatPanelIDForUserName = function(username)
	{
		var receiver = document.getElementById("ChatArea_" + this.ID + "_Panel_Receiver_" + username + "_ID");
		return receiver.value;
	};
	this.CreateChatPanelForUserName = function(username)
	{
		var receiver = document.getElementById("ChatArea_" + this.ID + "_Panel_Receiver_" + username + "_ID");
		if (receiver != null) return;
		
		var displayname = username;
		var panels = document.getElementById("ChatArea_" + this.ID + "_Panels");
		var html = panels.innerHTML;
		var index = panels.childNodes.length + 1;
		html += "<div id=\"ChatArea_" + this.ID + "_Panel_" + index + "\" class=\"ChatPanel\">";
		html += "<input type=\"hidden\" value=\"" + username + "\" id=\"ChatArea_" + this.ID + "_Panel_" + index + "_Receiver\">";
		html += "<input type=\"hidden\" value=\"" + displayname + "\" id=\"ChatArea_" + this.ID + "_Panel_" + index + "_Receiver_DisplayName\">";
		html += "<input type=\"hidden\" value=\"" + index + "\" id=\"ChatArea_" + this.ID + "_Panel_Receiver_" + username + "_ID\">";
		html += "<div class=\"ChatPanelTitle\">";
		html += "<a onclick=\"" + this.ID + ".ActivateChat(" + index + ");\" href=\"#\" class=\"Title\">" + displayname + "</a>";
		html += "<a href=\"http://www.psychatica.com/community/members/" + username + "\" class=\"Close\" target=\"_blank\">→</a>";
		html += "<a onclick=\"" + this.ID + ".CloseChat(" + index + "); return false;\" href=\"#\" class=\"Close\">×</a>";
		html += "</div>";
		html += "<div id=\"ChatArea_" + this.ID + "_Panel_" + index + "_History\" class=\"ChatPanelHistory\">&nbsp;</div>";
		html += "<div id=\"ChatArea_" + this.ID + "_Panel_" + index + "_Status\" class=\"ChatPanelStatus\">&nbsp;</div>";
		html += "<textarea onkeyup=\"" + this.ID + ".OnKeyUp(" + index + ", event);\" onkeydown=\"" + this.ID + ".OnKeyDown(" + index + ", event);\" placeholder=\"Type your message and press ENTER to send...\" type=\"text\" id=\"ChatArea_" + this.ID + "_Panel_" + index + "_Input\" class=\"ChatPanelInput\"></textarea>";
		html += "</div>";
		panels.innerHTML = html;
		
		var buttons = document.getElementById("ChatArea_" + this.ID + "_Buttons");
		html = buttons.innerHTML;
		
		html += "<a id=\"ChatArea_" + this.ID + "_Buttons_" + index + "_Button\" class=\"ChatBarButton\" onclick=\"" + this.ID + ".ActivateChat(" + index + "); return false;\" href=\"#\">";
		html += "<img src=\"<?php echo(System::ExpandRelativePath("~/community/members/")); ?>" + username + "/images/avatar/thumbnail.png\" class=\"ChatBarButtonAvatar\">";
		html += "<span class=\"ChatBarButtonText\">" + displayname + "</span>";
		html += "</a>";
		buttons.innerHTML = html;
	};
	this.ReceiveMessage = function(id, message)
	{
		var receiver = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Receiver");
		var receiver_name = receiver.value;
		var button = document.getElementById("ChatArea_" + this.ID + "_Buttons_" + id + "_Button");
		if (button.className == "ChatBarButton")
		{
			button.className = "ChatBarButton Attention";
		}
		
		var history = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_History");
		var html = "<div class=\"ChatPanelHistoryMessage\"><img title=\"" + this.GetDisplayNameForUserName(receiver_name) + "\" alt=\"" + this.GetDisplayNameForUserName(receiver_name) + "\" src=\"<?php echo(System::ExpandRelativePath("~/community/members/")); ?>" + receiver_name + "/images/avatar/thumbnail.png\" class=\"ChatPanelHistoryMessageAvatar\"><div class=\"ChatPanelHistoryMessageContent\">" + JH.Utilities.HtmlEncode(message) + "</div></div>";
		history.innerHTML += html;
		history.scrollTop = history.scrollTopMax;
	};
	this.AutoUpdatePresence = function(id)
	{
		var receiver = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Receiver");
		var receiver_name = receiver.value;
		
		var input = document.getElementById("ChatArea_" + this.ID + "_Panel_" + id + "_Input");
		if (input.value == "")
		{
			if (input.status != "active")
			{
				this.SendPresence(1, receiver_name);
				input.status = "active";
			}
		}
		else if (input.status != "composing")
		{
			input.status = "composing";
			this.SendPresence(2, receiver_name);
			
			if (input.pausedTimeout) window.clearTimeout(input.pausedTimeout);
			input.pausedTimeout = window.setTimeout(function(chatbar, input, username)
			{
				if (input.value == "")
				{
					input.status = "active";
					chatbar.SendPresence(1, username);
				}
				else
				{
					input.status = "paused";
					chatbar.SendPresence(3, username);
				}
			}, 5000, this, input, receiver_name);
		}
	};
	this.OnKeyDown = function(id, e)
	{
		this.AutoUpdatePresence(id);
		
		if (!e) e = window.event;
		if (e.keyCode == 13 && !e.shiftKey)
		{
			e.preventDefault();
			e.stopPropagation();
			
			this.SendMessage(id);
			return false;
		}
	};
	this.OnKeyUp = function(id, e)
	{
		this.AutoUpdatePresence(id);
	};
}