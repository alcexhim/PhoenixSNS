<?php
	namespace PhoenixSNS\Objects;
	use WebFX\System;
	
	class Comment
	{
		public $ParentComment;
		
		public $ID;
		public $Author;
		public $Title;
		public $Content;
		public $TimestampCreated;
		
		protected function GetCommentTableName()
		{
			return "comments";
		}
		public function Update()
		{
			global $MySQL;
			$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . $this->GetCommentTableName() . " SET comment_title = '" . $MySQL->real_escape_string($this->Title) . "', comment_content = '" . $MySQL->real_escape_string($this->Content) . "' WHERE comment_id = " . $this->ID;
			$result = $MySQL->query($query);
			return $result;
		}
		
		public function Render($comment_base_url = null)
		{
			$CurrentUser = User::GetCurrent();
			
			$ParentObjectType = 0;
			switch (get_class($this))
			{
				case "GroupTopicComment":
				{
					$ParentObjectType = 1;
					break;
				}
				case "JournalEntryComment":
				{
					$ParentObjectType = 2;
					break;
				}
			}
			
		?>
<div class="Comment">
	<div class="CommentTitle" id="Comment_<?php echo($this->ID); ?>_title"><?php echo($this->Title); ?></div>
	<div class="CommentEditor CommentTitle" id="Comment_<?php echo($this->ID); ?>_editor_title"><input id="Comment_<?php echo($this->ID); ?>_editor_title_input" type="text" value="<?php echo($this->Title); ?>" /></div>
	<div class="CommentContent" id="Comment_<?php echo($this->ID); ?>_content"><?php echo($this->Content); ?></div>
	<div class="CommentEditor" id="Comment_<?php echo($this->ID); ?>_editor_content">
		<textarea id="Comment_<?php echo($this->ID); ?>_editor_content_input"><?php echo($this->Content); ?></textarea>
	</div>
	<div class="CommentInformation">
		<span class="PostedBy">
		Posted by <span class="Author"><a href="<?php echo(System::ExpandRelativePath("~/community/members/" . $this->Author->ShortName)); ?>" target="_blank"><img src="<?php echo(System::ExpandRelativePath("~/community/members/" . $this->Author->ShortName . "/images/avatar/thumbnail.png")); ?>" style="height: 32px;" /> <?php
		mmo_display_user_badges_by_user($this->Author);
		echo($this->Author->LongName); ?></a></span>
		on <span class="Timestamp"><?php echo($this->TimestampCreated); ?></span>
		</span>
		<span class="CommentActions">
			<a id="Comment_<?php echo($this->ID); ?>_actions_like" href="#" onclick="CommentManager.SetPopularity(<?php echo($ParentObjectType); ?>, <?php echo($this->ID); ?>, 1); return false;">Like</a>
			|
			<a id="Comment_<?php echo($this->ID); ?>_actions_dislike" href="#" onclick="CommentManager.SetPopularity(<?php echo($ParentObjectType); ?>, <?php echo($this->ID); ?>, -1); return false;">Dislike</a>
			|
			<a id="Comment_<?php echo($this->ID); ?>_actions_reply" href="#" onclick="CommentManager.Reply(<?php echo($ParentObjectType); ?>, <?php echo($this->ID); ?>); return false;">Reply</a>
		<?php
			if ($this->Author->ID == $CurrentUser->ID)
			{
		?>
			|
			<a id="Comment_<?php echo($this->ID); ?>_actions_modify" href="#" onclick="CommentManager.BeginModify(<?php echo($ParentObjectType); ?>, <?php echo($this->ID); ?>); return false;">Modify</a>
			|
			<a id="Comment_<?php echo($this->ID); ?>_actions_delete" href="#" onclick="CommentManager.Delete(<?php echo($ParentObjectType); ?>, <?php echo($this->ID); ?>); return false;">Delete</a>
		<?php
			}
		?>
		</span>
	</div>
</div>
		<?php
		}
	}
?>