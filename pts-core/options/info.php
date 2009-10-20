<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2008 - 2009, Phoronix Media
	Copyright (C) 2008 - 2009, Michael Larabel

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

class info implements pts_option_interface
{
	public static function run($args)
	{
		$to_info = $args[0];
		echo "\n";

		if(pts_is_suite($to_info))
		{
			$suite = new pts_test_suite_details($to_info);
			echo "Suite Version: " . $suite->get_version() . "\n";
			echo "Maintainer: " . $suite->get_maintainer() . "\n";
			echo "Suite Type: " . $suite->get_suite_type() . "\n";
			echo "Unique Tests: " . $suite->get_unique_test_count() . "\n";
			echo "Suite Description: " . $suite->get_description() . "\n";
			echo "\n";
			echo $suite->pts_format_contained_tests_string();
			echo "\n";
		}
		else if(pts_is_test($to_info))
		{
			$test = new pts_test_profile_details($to_info);
			$test_title = $test->get_name();
			$test_version = $test->get_version();
			if(!empty($test_version))
			{
				$test_title .= " " . $test_version;
			}
			echo pts_string_header($test_title);

			echo "Profile Version: " . $test->get_test_profile_version() . "\n";
			echo "Maintainer: " . $test->get_maintainer() . "\n";
			echo "Test Type: " . $test->get_test_hardware_type() . "\n";
			echo "Software Type: " . $test->get_test_software_type() . "\n";
			echo "License Type: " . $test->get_license() . "\n";
			echo "Test Status: " . $test->get_status() . "\n";
			echo "Project Web-Site: " . $test->get_project_url() . "\n";

			$download_size = $test->get_download_size();
			if(!empty($download_size))
			{
				echo "Download Size: " . $download_size . " MB\n";
			}

			$environment_size = $test->get_environment_size();
			if(!empty($environment_size))
			{
				echo "Environment Size: " . $environment_size . " MB\n";
			}
			if(($el = pts_estimated_run_time($to_info)) > 0)
			{
				echo "Estimated Length: " . pts_format_time_string($el, "SECONDS", true, 60) . "\n";
			}

			echo "\nDescription: " . $test->get_description() . "\n";

			if(pts_test_installed($to_info))
			{
				$xml_parser = new pts_installed_test_tandem_XmlReader($to_info, false);
				$last_run = $xml_parser->getXMLValue(P_INSTALL_TEST_LASTRUNTIME);
				$avg_time = $xml_parser->getXMLValue(P_INSTALL_TEST_AVG_RUNTIME);

				if($last_run == "0000-00-00 00:00:00")
				{
					$last_run = "Never";
				}

				echo "\nTest Installed: Yes\n";
				echo "Last Run: " . $last_run . "\n";

				if($avg_time > 0)
				{
					echo "Average Run-Time: " . $avg_time . " Seconds\n";
				}
				if($last_run != "Never")
				{
					$times_run = $xml_parser->getXMLValue(P_INSTALL_TEST_TIMESRUN);

					if($times_run == null)
					{
						$times_run = 0;
					}

					echo "Times Run: " . $times_run . "\n";
				}
			}
			else
			{
				echo "\nTest Installed: No\n";
			}

			$dependencies = $test->get_dependencies();
			if(!empty($dependencies))
			{
				echo "\nSoftware Dependencies:\n";
				foreach($test->get_dependency_names() as $dependency)
				{
						echo "- " . $dependency . "\n";
				}
			}

			$associated_suites = $test->suites_using_this_test();
			if(count($associated_suites) > 0)
			{
				asort($associated_suites);
				echo "\nSuites Using This Test:\n";
				foreach($associated_suites as $suite)
				{
					echo "- " . $suite . "\n";
				}
			}
			echo "\n";
		}
		else if(($file = pts_find_result_file($to_info)) != false)
		{
			$tr = new pts_test_result_details($file, $to_info);

			echo "Title: " . $tr->get_title() . "\nIdentifier: " . $tr->get_saved_identifier() . "\nTest: " . $tr->get_suite() . "\n";
			echo "\nTest Result Identifiers:\n";

			foreach($tr->get_identifiers() as $id)
			{
				echo "- " . $id . "\n";
			}

			if(count($tr->get_unique_tests()) > 1)
			{
				echo "\nContained Tests:\n";
				foreach($tr->get_unique_tests() as $test)
				{
					echo "- " . $test . "\n";
				}
			}
			echo "\n";
		}
		else
		{
			echo "\n" . $to_info . " is not recognized.\n";
		}
	}
}

?>
