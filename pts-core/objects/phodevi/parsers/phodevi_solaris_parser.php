<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2009, Phoronix Media
	Copyright (C) 2009, Michael Larabel
	phodevi_solaris_parser.php: General parsing functions specific to the Windows OS

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class phodevi_solaris_parser
{
	public static function read_sun_ddu_dmi_info($find_objects, $args = "")
	{
		// Read Sun's Device Driver Utility for OpenSolaris
		$values = array();

		if(in_array(phodevi::read_property("system", "kernel-architecture"), array("i686", "x86_64")))
		{
			$dmi_info = "/usr/ddu/bin/i386/dmi_info";
		}
		else
		{
			$dmi_info = "/usr/ddu/bin/sparc/dmi_info";
		}

		if(is_executable($dmi_info) || is_executable(($dmi_info = "/usr/ddu/bin/dmi_info")))
		{
			$info = shell_exec($dmi_info . " " . $args . " 2>&1");
			$lines = explode("\n", $info);

			$find_objects = pts_to_array($find_objects);
			for($i = 0; $i < count($find_objects) && count($values) == 0; $i++)
			{
				$objects = explode(",", $find_objects[$i]);
				$this_section = "";

				if(count($objects) == 2)
				{
					$section = $objects[0];
					$object = $objects[1];
				}
				else
				{
					$section = "";
					$object = $objects[0];
				}

				foreach($lines as $line)
				{
					$line = pts_trim_explode(":", $line);
					$line_object = str_replace(" ", "", $line[0]);
					$this_value = (count($line) > 1 ? $line[1] : "");

					if(empty($this_value) && !empty($section))
					{
						$this_section = $line_object;
					}

					if($line_object == $object && ($this_section == $section || pts_proximity_match($section, $this_section)) && !empty($this_value) && $this_value != "Unknown")
					{
						array_push($values, $this_value);
					}
				}
			}
		}

		return $values;
	}
}

?>