var CommentManager =
{
	"SetPopularity": function(parentObjectType, commentID, popularity)
	{
		switch (popularity)
		{
			case 1:
			{
				alert('When implemented, will cause comment_popularity to increase! You will be able to filter comments below a certain popularity value.\r\n\r\nParent object type: ' + parentObjectType);
				break;
			}
			case -1:
			{
				alert('When implemented, will cause comment_popularity to decrease! You will be able to filter comments below a certain popularity value.\r\n\r\nParent object type: ' + parentObjectType);
				break;
			}
			default:
			{
				alert("Bad value for comment_popularity: a single user cannot cause a popularity change of more than 1 either way!");
				break;
			}
		}
	},
	"Reply": function(parentObjectType, commentID)
	{
		alert('When implemented, will set comment_parent_id to the ID of the parent comment. You will be able to reply to comments nested in multiple levels!\r\n\r\nParent object type: ' + parentObjectType);
	},
	"BeginModify": function(parentObjectType, commentID)
	{
		var editor_content = document.getElementById("Comment_" + commentID + "_editor_content");
		var editor_content_input = document.getElementById("Comment_" + commentID + "_editor_content_input");
		
		var editor_title = document.getElementById("Comment_" + commentID + "_editor_title");
		var editor_title_input = document.getElementById("Comment_" + commentID + "_editor_title_input");
		
		var title = document.getElementById("Comment_" + commentID + "_title");
		var content = document.getElementById("Comment_" + commentID + "_content");
		var button = document.getElementById("Comment_" + commentID + "_actions_modify");
		
		title.style.display = "none";
		content.style.display = "none";
		editor_content.style.display = "block";
		editor_title.style.display = "block";
		button.className = "Disabled";
		
		editor_content.onkeydown = function(e)
		{
			if (!e) e = window.event;
			if (e.keyCode == 13 && !e.shiftKey)
			{
				CommentManager.EndModify(parentObjectType, commentID);
				return false;
			}
		};
		
		editor_content_input.focus();
	},
	"EndModify": function(parentObjectType, commentID)
	{
		var editor_content = document.getElementById("Comment_" + commentID + "_editor_content");
		var editor_content_input = document.getElementById("Comment_" + commentID + "_editor_content_input");
		
		var editor_title = document.getElementById("Comment_" + commentID + "_editor_title");
		var editor_title_input = document.getElementById("Comment_" + commentID + "_editor_title_input");
		
		var title = document.getElementById("Comment_" + commentID + "_title");
		var content = document.getElementById("Comment_" + commentID + "_content");
		var button = document.getElementById("Comment_" + commentID + "_actions_modify");
		
		// TODO: send AJAX request to CommentModify.php
		$.ajax(
		{
			type: "POST",
			url: '/ajax/CommentModify.php',
			data:
			{
				'action': 'modify',
				'parentObjectType': parentObjectType,
				'commentID': commentID,
				'comment_title': editor_title_input.value,
				'comment_content': editor_content_input.value
			},
			dataType: "json",
			success: function(data)
			{
				if (data.result == "failure")
				{
					alert("Error occurred while attempting to save the data.  The server replied with: \r\n\r\n" + data.message);
					return;
				}
				else if (data.result == "success")
				{
					content.innerHTML = editor_content_input.value;
					title.innerHTML = editor_title_input.value;
					
					title.style.display = "block";
					content.style.display = "block";
					editor_title.style.display = "none";
					editor_content.style.display = "none";
					button.className = "";
				}
			},
			error: function(data)
			{
				alert("Error occurred while attempting to save the data");
				return;
			}
		});
	},
	"Delete": function(parentObjectType, commentID)
	{
		alert('When implemented, will allow you to remove the comment. Removing a comment will also remove any comments nested within.\r\n\r\nParent object type: ' + parentObjectType);
	}
};
