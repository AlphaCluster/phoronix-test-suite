<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2009 - 2013, Phoronix Media
	Copyright (C) 2009 - 2013, Michael Larabel

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

class remove_from_result_file implements pts_option_interface
{
	const doc_section = 'Result Management';
	const doc_description = 'This option is used if there is a set of test results you wish to remove/delete from a saved results file. The user must specify a saved results file and then they will be prompted to select the results identifier associated with the results they wish to remove.';

	public static function argument_checks()
	{
		return array(
		new pts_argument_check(0, array('pts_types', 'is_result_file'), null)
		);
	}
	public static function run($r)
	{
		$result = $r[0];

		$result_file = new pts_result_file($result);
		$result_file_identifiers = $result_file->get_system_identifiers();

		if(count($result_file_identifiers) < 2)
		{
			echo PHP_EOL . 'There are not multiple test runs in this result file.' . PHP_EOL;
			return false;
		}

		$remove_identifiers = explode(',', pts_user_io::prompt_text_menu('Select the test run(s) to remove', $result_file_identifiers, true));
		$keep_identifiers = array();

		foreach($result_file_identifiers as $identifier)
		{
			if(!in_array($identifier, $remove_identifiers))
			{
				array_push($keep_identifiers, $identifier);
			}
		}

		foreach(array('test-logs', 'system-logs', 'installation-logs') as $dir_name)
		{
			foreach($remove_identifiers as $remove_identifier)
			{
				if(is_dir(PTS_SAVE_RESULTS_PATH . $r[0] . '/' . $dir_name . '/' . $remove_identifier))
				{
					pts_file_io::delete(PTS_SAVE_RESULTS_PATH . $r[0] . '/' . $dir_name . '/' . $remove_identifier, null, true);
				}
			}
		}

		$extract_select = new pts_result_merge_select($result, $keep_identifiers);
		$extract_result = pts_merge::merge_test_results($extract_select);

		pts_client::save_test_result($r[0] . '/composite.xml', $extract_result);
		pts_client::display_web_page(PTS_SAVE_RESULTS_PATH . $r[0] . '/index.html');
	}
}

?>
