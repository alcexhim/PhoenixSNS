<?php
	// =============================================================================
	// Phast bootstrapper - loads the application modules and executes Phast
	// Copyright (C) 2013-2014  Mike Becker
    // 
	// This program is free software: you can redistribute it and/or modify
	// it under the terms of the GNU General Public License as published by
	// the Free Software Foundation, either version 3 of the License, or
	// (at your option) any later version.
    // 
	// This program is distributed in the hope that it will be useful,
	// but WITHOUT ANY WARRANTY; without even the implied warranty of
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	// GNU General Public License for more details.
    // 
	// You should have received a copy of the GNU General Public License
	// along with this program.  If not, see <http://www.gnu.org/licenses/>.
	// =============================================================================
	
	// Load the Phast content (which also include_once's the system modules and
	// other Phast-specific stuff)
	require_once("Phast/System.inc.php");
	
	// Bring in the Phast\System and Phast\IncludeFile classes so we can simply refer
	// to them (in this file only) as "System" and "IncludeFile", respectively, from
	// now on
	use Phast\System;
	use Phast\IncludeFile;
	
	// We need to set the root path of the Web site. It's usually something like
	// /var/www/yourdomain.com.
	System::SetApplicationPath(dirname(__FILE__));
	
	// Tell Phast that we are ready to launch the application. This searches the entire
	// directory hierarchy for Phast files, loading *.phpx files as Phast XML files and
	// *.phpx.php files as PHP code-behind files. We may decide to use the *.ctlx extension
	// (and its associated *.ctlx.php) for PHPX controls.
	System::Launch();
?>