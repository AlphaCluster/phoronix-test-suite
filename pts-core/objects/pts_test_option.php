<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2008 - 2011, Phoronix Media
	Copyright (C) 2008 - 2011, Michael Larabel
	pts_test_option: An object used for storing a test option and its possible values

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

class pts_test_option
{
	private $identifier = null;
	private $option_name = null;
	private $prefix = null;
	private $postfix = null;
	private $default_entry = -1;
	private $options = array();

	public function __construct($identifier, $option)
	{
		$this->identifier = $identifier;
		$this->option_name = $option;
	}
	public function set_option_prefix($prefix)
	{
		$this->prefix = $prefix;
	}
	public function set_option_postfix($postfix)
	{
		$this->postfix = $postfix;
	}
	public function set_option_default($default_node)
	{
		$default_node--;
		if(isset($this->options[$default_node]))
		{
			$this->default_entry = $default_node;
		}
	}
	public function get_identifier()
	{
		return $this->identifier;
	}
	public function get_name()
	{
		return $this->option_name;
	}
	public function get_option_prefix()
	{
		return $this->prefix;
	}
	public function get_option_postfix()
	{
		return $this->postfix;
	}
	public function get_option_default_raw()
	{
		return $this->default_entry == -1 ? 0 : $this->default_entry;
	}
	public function get_option_default()
	{		
		return $this->default_entry == -1 ? $this->option_count() - 1 : $this->default_entry;
	}
	public function add_option($name, $value, $message)
	{
		array_push($this->options, array($name, $value, $message));
	}
	public function get_options_array()
	{
		return $this->options;
	}
	public function get_all_option_names()
	{
		$names = array();

		for($i = 0; $i < $this->option_count(); $i++)
		{
			array_push($names, $this->get_option_name($i));
		}

		return $names;
	}
	public function get_all_option_names_with_messages()
	{
		$names = array();

		for($i = 0; $i < $this->option_count(); $i++)
		{
			$user_msg = $this->get_option_message($i);

			array_push($names, $this->get_option_name($i) . (!empty($user_msg) ? ' [' . $user_msg . ']' : null));
		}

		return $names;
	}
	public function get_option_name($index)
	{
		return isset($this->options[$index][0]) ? $this->options[$index][0] : null;
	}
	public function get_option_value($index)
	{
		return isset($this->options[$index][1]) ? $this->options[$index][1] : null;
	}
	public function get_option_message($index)
	{
		return isset($this->options[$index][2]) ? $this->options[$index][2] : null;
	}
	public function option_count()
	{
		return count($this->options);
	}
	public function format_option_value_from_input($input)
	{
		return $this->get_option_prefix() . $input . $this->get_option_postfix();
	}
	public function format_option_display_from_input($input)
	{
		$name = $this->get_name();

		return $name != null && $input != null ? $name . ': ' . $input : null;
	}
	public function format_option_value_from_select($select_pos)
	{
		$input = $this->get_option_value($select_pos);

		return $this->format_option_value_from_input($input);
	}
	public function format_option_display_from_select($select_pos)
	{
		$display_name = $this->get_option_name($select_pos);

		if(($cut_point = strpos($display_name, '(')) > 1 && strpos($display_name, ')') > $cut_point)
		{
			$display_name = trim(substr($display_name, 0, $cut_point));
		}

		return $this->format_option_display_from_input($display_name);
	}
	public function is_valid_select_choice($select_pos)
	{
		$valid = false;

		if(is_numeric($select_pos) && $select_pos >= 0 && $select_pos < $this->option_count())
		{
			$valid = $select_pos;
		}
		else if(in_array($select_pos, $this->get_all_option_names()))
		{
			$match_made = false;

			for($i = 0; $i < $this->option_count() && !$match_made; $i++)
			{
				if($this->get_option_name($i) == $select_pos)
				{
					$valid = $i;
					$match_made = true;
				}
			}
		}

		return $valid;
	}
	public function parse_selection_choice_input($input, $use_default_on_empty = true)
	{
		$return_keys = array();

		if($input === '0')
		{
			array_push($return_keys, 0);
		}
		else
		{
			foreach(pts_strings::comma_explode($input) as $input_choice)
			{
				if($input_choice == $this->option_count() || $input_choice == 'Test All Options')
				{
					// Add all options
					foreach(array_keys($this->options) as $i)
					{
						array_push($return_keys, $i);
					}
					break;
				}
				else if(($c = $this->is_valid_select_choice($input_choice)) !== false)
				{
					array_push($return_keys, $c);
				}
			}
		}

		$return_keys = array_unique($return_keys);
		sort($return_keys);

		if($use_default_on_empty && count($return_keys) == 0)
		{
			// Use the default as no valid options were presented
			array_push($return_keys, $this->get_option_default());
		}

		return $return_keys;
	}
}

?>
