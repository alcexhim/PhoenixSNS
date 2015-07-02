<table style="width: 100%">
	<tr>
		<td style="width: 25%">
			<?php
				$categoryName = $path[3];
			?>
			<div class="ActionList">
				<?php
				if ($categoryName == "")
				{
				?>
					<span class="Selected">All Files</span>
				<?php
				}
				else
				{
				?>
					<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($thisuser->ShortName); ?>/storage">All Files</a>
				<?php
				}
				?>
				
				<?php
					$categories = FileCategory::Get();
					foreach ($categories as $category)
					{
						if ($categoryName == $category->Name)
						{
				?>
					<span class="Selected"><?php echo($category->Title); ?></span>
				<?php
						}
						else
						{
				?>
					<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/community/members/<?php echo($thisuser->ShortName); ?>/storage/<?php echo($category->Name); ?>"><?php echo($category->Title); ?></a>
				<?php
						}
					}
				?>
			</div>
		</td>
		<td>
			<?php
			if ($categoryName != "")
			{
				$category = FileCategory::GetByName($categoryName);
			}
			else
			{
				$category = null;
			}
			
			$files = File::GetByUser($thisuser, $category);
			$count = count($files);
			?>
			
			<div class="ProfilePage">
				<div class="ProfileTitle">
					<span class="ProfileUserName"><?php
						if ($count == 1)
						{
							echo ("There is 1 file");
						}
						else
						{
							echo("There are " . $count . " files");
						}
					?></span>
					<span class="ProfileControlBox">
						<?php
						if ($thisuser->ID == $CurrentUser->ID)
						{
						?>
						<a href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/files/create.mmo">Upload file</a>
						<?php
						}
						?>
					</span>
				</div>
				<div class="ProfileContent">
					<table style="width: 100%" border="1">
						<tr>
							<td>File name</td>
							<td style="width: 25%">Category</td>
							<td style="width: 40%">Created on</td>
						</tr>
						<?php
						foreach ($files as $file)
						{
						?>
						<tr>
							<td><a style="display: block;" target="_blank" href="<?php echo(System::$Configuration["Application.BasePath"]); ?>/files/<?php echo($file->ID); ?>"><?php echo($file->Name); ?></a></td>
							<td><?php echo($file->Category->Title); ?></td>
							<td><?php echo($file->CreationTimestamp); ?></td>
						</tr>
						<?php
						}
						?>
					</table>
				</div>
			</div>
		</td>
	</tr>
</table>