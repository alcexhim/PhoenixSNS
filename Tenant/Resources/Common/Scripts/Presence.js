// http://css-tricks.com/jquery-php-chat/

var chat_autoScroll = true;
var chat_scriptFileName = "/ajax/presence.php";

function chat_sendChat(message)
{
	$.ajax(
	{
		type: "POST",
		url: chat_scriptFileName,
		data:
		{
			'action': 'send',
			'message': message
		},
		dataType: "json",
		success: function(data)
		{
		}
	});
}

function presence_BroadcastConnect()
{
	$.ajax(
	{
		type: "POST",
		url: chat_scriptFileName,
		data:
		{
			'action': 'connect'
		},
		dataType: "json",
		success: function(data)
		{
			if (data.result == "success")
			{
			}
		}
	});
}
function presence_BroadcastDisconnect()
{
	if (typeof(xmpp) != "undefined") xmpp.NotifyAvailable(false);

	$.ajax(
	{
		type: "POST",
		url: chat_scriptFileName,
		data:
		{
			'action': 'disconnect'
		},
		dataType: "json",
		success: function(data)
		{
			if (data.result == "success")
			{
			}
		}
	});
}

function presence_RetrievePresence()
{
	$.ajax(
	{
		type: "POST",
		url: chat_scriptFileName,
		data:
		{
			'action': 'receive'
		},
		dataType: "json",
		success: function(data)
		{
			if (data.result == "success")
			{
				/*
				var d = $("#personas");
				var di = d.get(0);
				var personas = data.personas;
				html = "";
				for (var i = 0; i < personas.length; i++)
				{
					var persona = personas[i];
					html += persona.code;
				}
				di.innerHTML = html;
				*/
				
				var messages = data.messages;
				var html = "";
				for (var i = 0; i < messages.length; i++)
				{
					html += "<tr>";
					html += "<td class=\"ChatUserName\"><a href=\"" + messages[i].url + "\" target=\"_blank\" onclick=\"DisplayProfilePage('" + messages[i].shortName + "'); return false;\">" + messages[i].longName + "</a></td>";
					html += "<td>:</td>";
					html += "<td class=\"ChatMessage\">" + messages[i].message + "</td>";
					html += "</tr>";
					
					document.getElementById("persona_messageBalloon_" + messages[i].memberID).style.display = "block";
					document.getElementById("persona_messageBalloon_" + messages[i].memberID).innerHTML = messages[i].message;
				}
				
				document.getElementById("chatLog").innerHTML += html;
				
				if (this.autoScroll)
				{
					document.getElementById('chatLogWindow').scrollTop = document.getElementById('chatLogWindow').scrollHeight;
				}
			}
			window.setTimeout(function() { presence_RetrievePresence(); }, 5000);
		}
	});
}

window.onbeforeunload = function()
{
	presence_BroadcastDisconnect();
};