<?php
	namespace PhoenixSNS\Controls;
	
	use WebFX\WebControl;
	use WebFX\System;
	
	\Enum::Create("PhoenixSNS\\Controls\ChatBarPosition", "Top", "Bottom");
	
	class ChatPanelMessage
	{
		public $UserName;
		public $DisplayName;
		public $ImageURL;
		public $Content;
		
		public function __construct($userName, $displayName, $content)
		{
			$this->UserName = $userName;
			$this->DisplayName = $displayName;
			$this->Content = $content;
		}
	}
	class ChatPanel
	{
		public $UserName;
		public $DisplayName;
		public $ImageURL;
		public $Messages;
		
		public function __construct($userName, $displayName = null, $imageURL = null)
		{
			$this->UserName = $userName;
			if ($displayName == null) $displayName = $userName;
			$this->DisplayName = $displayName;
			$this->ImageURL = $imageURL;
			$this->Messages = array();
		}
	}
	class ChatBarBuddy
	{
		public $UserName;
		public $DisplayName;
		public $ImageURL;
		
		public function __construct($userName, $displayName = null, $imageURL = null)
		{
			$this->UserName = $userName;
			if ($displayName == null) $displayName = $userName;
			$this->DisplayName = $displayName;
			$this->ImageURL = $imageURL;
		}
	}
	class ChatBar extends WebControl
	{
		public $Buddies;
		public $UserName;
		public $Panels;
		public $PlaceholderText;
		public $Position;
		
		public function __construct($id, $userName)
		{
			parent::__construct($id);
			$this->Buddies = array();
			$this->UserName = $userName;
			$this->Panels = array();
			$this->PlaceholderText = "Type your message and press ENTER to send...";
			$this->Position = ChatBarPosition::Bottom;
		}
		
		protected function RenderContent()
		{
		?>
		<div class="ChatBar<?php switch($this->Position)
		{
			case ChatBarPosition::Top:
			{
				echo(" Top");
				break;
			}
			case ChatBarPosition::Bottom:
			{
				echo(" Bottom");
				break;
			}
		}?>" id="ChatArea_<?php echo($this->ID); ?>">
			&nbsp;
			<div class="ChatPanels" id="ChatArea_<?php echo($this->ID); ?>_Panels">
				<div class="ChatPanel BuddyListPanel" id="ChatArea_<?php echo($this->ID); ?>_BuddyListPanel">
				<?php
					foreach ($this->Buddies as $buddy)
					{
						?>
							<a href="#" onclick="<?php echo($this->ID); ?>.CreateChat('<?php echo($buddy->UserName); ?>', '<?php echo($buddy->DisplayName); ?>', '<?php echo($buddy->ImageURL); ?>'); return false;"><?php echo($buddy->DisplayName); ?></a>
						<?php
					}
				?>
				</div>
			<?php
				$i = 0;
				foreach ($this->Panels as $panel)
				{
			?>
				<div class="ChatPanel" id="ChatArea_<?php echo($this->ID); ?>_Panel_<?php echo($i); ?>">
					<input type="hidden" id="ChatArea_<?php echo($this->ID); ?>_Panel_<?php echo($i); ?>_Receiver" value="<?php echo($panel->UserName); ?>" />
					<input type="hidden" id="ChatArea_<?php echo($this->ID); ?>_Panel_<?php echo($i); ?>_Receiver_DisplayName" value="<?php echo($panel->DisplayName); ?>" />
					<input type="hidden" id="ChatArea_<?php echo($this->ID); ?>_Panel_Receiver_<?php echo($panel->UserName); ?>_ID" value="<?php echo($i); ?>" />
					<div class="ChatPanelTitle">
						<a class="Title" href="#" onclick="<?php echo($this->ID); ?>.ActivateChat(<?php echo($i); ?>); return false;"><?php echo($panel->DisplayName); ?></a>
					<?php
					if ($panel->PopoutURL != null)
					{
					?>
						<a target="_blank" class="Close" href="<?php echo(System::ExpandRelativePath($panel->PopoutURL)); ?>"><i class="fa fa-external-link"></i></a>
					<?php
					}
					?>
						<a class="Close" href="#" onclick="<?php echo($this->ID); ?>.CloseChat(<?php echo($i); ?>); return false;"><i class="fa fa-times"></i></a>
					</div>
					<div class="ChatPanelHistory" id="ChatArea_<?php echo($this->ID); ?>_Panel_<?php echo($i); ?>_History">
					<?php
					foreach ($panel->Messages as $message)
					{
						?>
						<div class="ChatPanelHistoryMessage">
							<img class="ChatPanelHistoryMessageAvatar" src="<?php echo(System::ExpandRelativePath($message->ImageURL)); ?>" alt="<?php echo($message->DisplayName); ?>" title="<?php echo($message->DisplayName); ?>" />
							<div class="ChatPanelHistoryMessageContent"><?php echo($message->Content); ?></div>
						</div>
						<?php
					}
					?>
					</div>
					<div class="ChatPanelStatus" id="ChatArea_<?php echo($this->ID); ?>_Panel_<?php echo($i); ?>_Status">&nbsp;</div>
					<textarea class="ChatPanelInput" id="ChatArea_<?php echo($this->ID); ?>_Panel_<?php echo($i); ?>_Input" type="text" placeholder="<?php echo($this->PlaceholderText); ?>" onkeydown="<?php echo($this->ID); ?>.OnKeyDown(<?php echo($i); ?>, event);" onkeyup="<?php echo($this->ID); ?>.OnKeyUp(<?php echo($i); ?>, event);"></textarea>
				</div>
			<?php
					$i++;
				}
			?>
			</div>
			<div class="ChatButtons" id="ChatArea_<?php echo($this->ID); ?>_Buttons">
				<a href="#" class="ChatBarButton" id="ChatArea_<?php echo($this->ID); ?>_BuddyListButton" onclick="<?php echo($this->ID); ?>.ActivateBuddyList(); return false;"><span class="ChatBarButtonText">Buddy List</span></a>
			<?php
				$i = 0;
				foreach ($this->Panels as $panel)
				{
			?>
				<a href="#" class="ChatBarButton" id="ChatArea_<?php echo($this->ID); ?>_Buttons_<?php echo($i); ?>_Button" onclick="<?php echo($this->ID); ?>.ActivateChat(<?php echo($i); ?>); return false;">
					<img class="ChatBarButtonAvatar" src="<?php echo(System::ExpandRelativePath($panel->ImageURL)); ?>" />
					<span class="ChatBarButtonText"><?php echo($panel->DisplayName); ?></span>
				</a>
			<?php
					$i++;
				}
			?>
			</div>
			<script type="text/javascript">var <?php echo($this->ID); ?> = new ChatBar("<?php echo($this->ID); ?>", "<?php echo($this->UserName); ?>");</script>
		</div>
		<?php
		}
	}
?>