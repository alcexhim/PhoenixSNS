<?php
	global $RootPath;
	$RootPath = dirname(__FILE__) . "/../../../..";
	
	require_once("WebFX/WebFX.inc.php");
	use WebFX\System;
	
	header("Content-Type: text/javascript");
?>
function AvatarBase(id)
{
	this.ID = id;
	this.Width = 400;
	this.Height = 1516;
}
AvatarBase.GetByID = function(id)
{
	return new AvatarBase(id);
};

function AvatarView(id)
{
	this.ID = id;
}
AvatarView.GetByID = function(id)
{
	return new AvatarView(id);
};

function AvatarSlice(parent, name)
{
	this.Parent = parent;
	this.Name = name;
	this.Rotate = function(value)
	{
		var sliceKey = "Avatar_" + this.Parent.Name + "_body_Slices_" + this.Name;
		var slice = document.getElementById(sliceKey);
		if (!slice)
		{
			console.error("slice not found: " + sliceKey);
			return;
		}
		slice.style.transform = "rotate(" + value + "deg)";
	};
}

function Avatar(name, base)
{
	this.Name = name;
	this.AnimateLoadingStep = 0.1;
	this.HTIMERSpeak = null;
	if (!base)
	{
		this.Base = AvatarBase.GetByID(1);
	}
	else
	{
		this.Base = base;
	}
	this.View = AvatarView.GetByID(1);
	this.AnimateLoadingInternal = function(avatar)
	{
		var fill = document.getElementById("Avatar_" + avatar.Name + "_loading_fill");
		if (fill.style.opacity >= 1)
		{
			avatar.AnimateLoadingStep = -0.1;
		}
		else if (fill.style.opacity <= 0)
		{
			avatar.AnimateLoadingStep = 0.1;
		}
		fill.style.opacity = parseFloat(fill.style.opacity) + avatar.AnimateLoadingStep;
		avatar.AnimateLoadingHTIMER = window.setTimeout(avatar.AnimateLoadingInternal, 50, avatar);
	};
	this.Speak = function(text)
	{
		var bubble = document.getElementById("Avatar_" + this.Name + "_chatbubble");
		bubble.style.opacity = 0.0;
		bubble.style.display = "block";
		bubble.innerHTML = text;
		
		if (this.HTIMERSpeak != null) window.clearTimeout(this.HTIMERSpeak);
		Avatar.AnimateChatBubble(bubble, 0.1);
		this.HTIMERSpeak = window.setTimeout(function()
		{
			Avatar.AnimateChatBubble(bubble, -0.1);
		}, Avatar.ChatTimeout);
	};
	
	this.ItemEquipped = new Callback(this);
	this.ItemUnequipped = new Callback(this);
	this.LoadSucceeded = new Callback(this);
	this.LoadFailed = new Callback(this);
	
	this.SetErrorDescription = function(text)
	{
		var Avatar_Error = document.getElementById("Avatar_" + this.Name + "_error");
		Avatar_Error.title = text;
	};
	this.Equip = function(itemid, customData)
	{
		$.ajax(
		{
			type: "POST",
			url: "<?php echo(System::ExpandRelativePath("~/ajax/ItemDetail.php")); ?>",
			data:
			{
				'action': 'equip',
				'item_id': itemid
			},
			dataType: "json",
			success: function(sender, data)
			{
				if (data.result == "success")
				{
					sender.ItemEquipped.Execute({ "Item": data.content, "Data": customData });
				}
				else
				{
					alert("An unexpected error has occurred");
				}
			}.PrependArgument(this),
			error: function()
			{
				alert("An unexpected error has occurred");
			}
		});
	};
	this.Unequip = function(itemid, customData)
	{
		$.ajax(
		{
			type: "POST",
			url: "<?php echo(System::ExpandRelativePath("~/ajax/ItemDetail.php")); ?>",
			data:
			{
				'action': 'unequip',
				'item_id': itemid
			},
			dataType: "json",
			success: function(sender, data)
			{
				if (data.result == "success")
				{
					sender.ItemUnequipped.Execute({ "Item": data.content, "Data": customData });
				}
				else
				{
					alert("An unexpected error has occurred");
				}
			}.PrependArgument(this),
			error: function()
			{
				alert("An unexpected error has occurred");
			}
		});
	};
	
	this.HTIMERAnimation = null;
	/// <summary>
	///	Plays the named animation on the current avatar renderer.
	/// </summary>
	/// <param name="name"></param>
	this.Play = function(name)
	{
		if (this.HTIMERAnimation)
		{
			window.clearTimeout(this.HTIMERAnimation);
			this.Reset();
		}
		
		var animation = Avatar.Animations.GetByName(name);
		this.PlayInternal(animation, 0, 0);
	};
	this.PlayInternal = function(animation, currentFrameIndex, currentKeyframeIndex)
	{
		var millisecondsBetweenFrames = animation.FrameRate / 1000;
		var next = animation.Keyframes[currentKeyframeIndex];
		this.HTIMERAnimation = window.setTimeout(function(animation, avatar, currentFrameIndex, currentKeyframeIndex)
		{
			if (currentKeyframeIndex + 1 < animation.Keyframes.length)
			{
				// output the current frame
				console.log("current frame: " + currentFrameIndex + "; current keyframe: " + currentKeyframeIndex);
				
				var frame = animation.Keyframes[currentKeyframeIndex];
				var next = animation.Keyframes[currentKeyframeIndex + 1];
				
				for (var i = 0; i < frame.Slices.length; i++)
				{
					var totalFramesUntilNext = (next.Time - frame.Time);
					var relativeFrameIndex = (currentFrameIndex - frame.Time);
					var interpolationPercent = (relativeFrameIndex / totalFramesUntilNext);
					var kvf = (next.Slices[i].Rotation - frame.Slices[i].Rotation);
					var val = interpolationPercent * kvf;
					
					avatar.Slices.GetByName(frame.Slices[i].Name).Rotate(val);
					console.log("slice '" + frame.Slices[i].Name + "' rotated " + val + " degrees");
					console.log("TFUN: " + totalFramesUntilNext + "; RFI: " + relativeFrameIndex + "; IP: " + interpolationPercent + "; VAL: " + val + "; KVF: " + kvf);
					console.log("NSR: " + next.Slices[i].Rotation + "; FSR: " + frame.Slices[i].Rotation);
				}
				
				// see if the next keyframe will be reached
				if (animation.Keyframes[currentKeyframeIndex + 1].Time == currentFrameIndex + 1)
				{
					// it will be reached, so set this as the next keyframe
					currentKeyframeIndex++;
				}
				avatar.PlayInternal(animation, currentFrameIndex + 1, currentKeyframeIndex);
			}
			else
			{
				if (animation.Continuous === true)
				{
					// go back to the beginning and start all over
					this.PlayInternal(animation, 0, 0);
				}
				
				var frame = animation.Keyframes[currentKeyframeIndex];
				for (var i = 0; i < frame.Slices.length; i++)
				{
					avatar.Slices.GetByName(frame.Slices[i].Name).Rotate(frame.Slices[i].Rotation);
				}
			}
		}, millisecondsBetweenFrames, animation, this, currentFrameIndex, currentKeyframeIndex);
	};
	
	this.Reset = function()
	{
		alert("TODO: Reset the rotation of all slices on the avatar to 0.");
	};
	
	this.Refresh = function()
	{
		var fill = document.getElementById("Avatar_" + this.Name + "_loading_fill");
		fill.style.opacity = 1;
		fill.style.width = this.Base.Width + "px";
		fill.style.height = this.Base.Height + "px";
		
		var erra = document.getElementById("Avatar_" + this.Name + "_error");
		erra.style.display = "none";
		erra.style.width = this.Base.Width + "px";
		erra.style.height = this.Base.Height + "px";
		
		var loding = document.getElementById("Avatar_" + this.Name + "_loading");
		loding.style.display = "block";
		loding.style.width = this.Base.Width + "px";
		loding.style.height = this.Base.Height + "px";
		
		var body = document.getElementById("Avatar_" + this.Name + "_body");
		body.style.display = "none";
		body.style.width = this.Base.Width + "px";
		body.style.height = this.Base.Height + "px";
		
		this.AnimateLoadingInternal(this);
		
		// set image list
		var images =
		[
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/head.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/torso.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/arm_right_upper.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/arm_right_lower.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/hand_right.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/arm_left_upper.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/arm_left_lower.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/hand_left.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/leg_right_upper.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/leg_right_lower.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/foot_right.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/leg_left_upper.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/leg_left_lower.png",
			"<?php echo(System::ExpandRelativePath("~/images/avatar/bases/")); ?>" + this.Base.ID + "/" + this.View.ID + "/slices/foot_left.png"
		];
		var data =
		[
			"head", "torso", "arm_right_upper", "arm_right_lower", "hand_right", "arm_left_upper", "arm_left_lower", "hand_left", "leg_right_upper",
			"leg_right_lower", "foot_right", "leg_left_upper", "leg_left_lower", "foot_left"
		];
		var imageLoader = new ImageLoader(images);
		var parent = this;
		imageLoader.Data = data;
		imageLoader.OnError = function(sender, e)
		{
			console.error("image load failed for " + e.Image.src);
			window.setTimeout(Avatar.StopLoading, 2000, sender.CallbackData, true);
		};
		imageLoader.OnLoadCompleted = function(sender, e)
		{
			for (var i = 0; i < images.length; i++)
			{
				var w = document.getElementById("Avatar_" + parent.Name + "_body_Slices_" + data[i]);
				w.style.backgroundImage = "url('" + images[i] + "')";
			}
			window.setTimeout(Avatar.StopLoading, 2000, sender.CallbackData, false);
		};
		imageLoader.CallbackData = this;
		imageLoader.Load();
	};
	
	var avvie = this;
	this.Slices =
	{
		"GetByName": function(name)
		{
			return new AvatarSlice(avvie, name);
		}
	};
	this.Refresh();
}
Avatar.StopLoading = function(avatar, error)
{
	var body = document.getElementById("Avatar_" + avatar.Name + "_body");
	var loading = document.getElementById("Avatar_" + avatar.Name + "_loading");
	var derror = document.getElementById("Avatar_" + avatar.Name + "_error");
	
	loading.style.display = "none";
	
	if (error === true)
	{
		derror.style.display = "block";
		avatar.SetErrorDescription("One or more avatar slice images failed to load.");
	}
	else
	{
		body.style.display = "block";
	}
	
	window.clearTimeout(avatar.AnimateLoadingHTIMER);
	
	if (error === true)
	{
		avatar.LoadFailed.Execute();
	}
	else
	{
		avatar.LoadSucceeded.Execute();
	}
};
Avatar.AnimateChatBubble = function(bubble, step)
{
	var opacity = parseFloat(bubble.style.opacity);
	opacity += step;
	bubble.style.opacity = opacity;
	
	if (bubble.style.opacity == 1.0 || bubble.style.opacity == 0.0) return;
	window.setTimeout(Avatar.AnimateChatBubble, 50, bubble, step);
};
Avatar.ChatTimeout = 3000;
Avatar.SpeechSequenceCurrentFrame = 0;
Avatar.PlaySpeechSequence = function(sequence, repeat, repeat_delay)
{
	if (Avatar.SpeechSequenceCurrentFrame < sequence.length)
	{
		window.setTimeout(Avatar.PlaySpeechSequenceInternal, sequence[Avatar.SpeechSequenceCurrentFrame].delay, sequence, repeat, repeat_delay);
	}
}
Avatar.PlaySpeechSequenceInternal = function(sequence, repeat, repeat_delay)
{
	sequence[Avatar.SpeechSequenceCurrentFrame].target.Speak(sequence[Avatar.SpeechSequenceCurrentFrame].text);
	Avatar.SpeechSequenceCurrentFrame++;
	if (Avatar.SpeechSequenceCurrentFrame < sequence.length)
	{
		window.setTimeout(Avatar.PlaySpeechSequenceInternal, sequence[Avatar.SpeechSequenceCurrentFrame].delay, sequence, repeat, repeat_delay);
	}
	else if (Avatar.SpeechSequenceCurrentFrame == sequence.length)
	{
		Avatar.SpeechSequenceCurrentFrame = 0;
		if (repeat >= -1)
		{
			if (repeat > 0) repeat--;
			window.setTimeout(Avatar.PlaySpeechSequenceInternal, repeat_delay + sequence[Avatar.SpeechSequenceCurrentFrame].delay, sequence, repeat, repeat_delay);
		}
	}
};