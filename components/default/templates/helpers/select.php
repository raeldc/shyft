<?php
/**
 * @category	Shyft
 * @package		Shyft_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2010 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.shyfted.com
 */

/**
 * Template Listbox Helper
 *
 * @author		Israel Canasa <shyft@me.com>
 * @category	Shyft
 * @package		Shyft_Template
 * @subpackage	Helper
 */
class ComDefaultTemplateHelperSelect extends KTemplateHelperAbstract
{
	/**
	 * Generates an HTML select option
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function option( $config = array() )
	{
		$config = new KConfig($config);
		$config->append(array(
			'value' 	=> null,
			'text'   	=> '',
			'disable'	=> false,
			'attribs'	=> array(),
		));

		$option = new stdClass;
		$option->value 	  = $config->value;
		$option->text  	  = trim( $config->text ) ? $config->text : $config->value;
		$option->disable  = $config->disable;
		$option->attribs  = $config->attribs;
		
		return $option;
	}
	
	/**
	 * Generates an HTML select list
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function optionlist($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'options' 	=> array(),
			'name'   	=> 'id',
			'attribs'	=> array('size' => 1),
			'selected'	=> null,
			'translate'	=> false
		));
		
		$name    = $config->name;
		$attribs = KHelperArray::toString($config->attribs);
		
		$html = array();
		$html[] = '<select name="'. $name .'" '. $attribs .'>';
		
		foreach($config->options as $option)
		{
			$value  = $option->value;
			$text   = $config->translate ? text( $option->text ) : $option->text;

			$extra = '';
			if(isset($option->disable) && $option->disable) {
				$extra .= 'disabled="disabled"';
			}
				
			if(isset($option->attribs)) {
				$extra .= ' '.KHelperArray::toString($option->attribs);;
			}
			
			if(!is_null($config->selected))
			{
				if ($config->selected instanceof KConfig)
				{
					foreach ($config->selected as $selected)
					{
						$sel = is_object( $selected ) ? $selected->value : $selected;
						if ($value == $sel)
						{
							$extra .= 'selected="selected"';
							break;
						}
					}
				} 
				else $extra .= ((string) $value == $config->selected ? ' selected="selected"' : '');
			}
				
			$html[] = '<option value="'. $value .'" '. $extra .'>' . $text . '</option>';
		}
		
		$html[] = '</select>';

		return implode(PHP_EOL, $html);
	}
	
	/**
	 * Generates an HTML radio list
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function radiolist( $config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'list' 		=> null,
			'name'   	=> 'id',
			'attribs'	=> array(),
			'key'		=> 'id',
			'text'		=> 'title',
			'selected'	=> null,
			'translate'	=> false
		));
		
		$name    = $config->name;
		$attribs = KHelperArray::toString($config->attribs);

		$html = array();
		foreach($config->list as $row)
		{
			$key  = $row->{$config->key};
			$text = $config->translate ? text( $row->{$config->text} ) : $row->{$config->text};
			$id	  = isset($row->id) ? $row->id : null;

			$extra = '';
			
			if ($config->selected instanceof KConfig)
			{
				foreach ($config->selected as $value)
				{
					$sel = is_object( $value ) ? $value->{$config->key} : $value;
					if ($key == $sel)
					{
						$extra .= 'selected="selected"';
						break;
					}
				}
			} 
			else $extra .= ($key == $config->selected ? 'checked="checked"' : '');
				
			$html[] = '<input type="radio" name="'.$name.'" id="'.$name.$id.'" value="'.$key.'" '.$extra.' '.$attribs.' />';
			$html[] = '<label for="'.$name.$id.'">'.$text.'</label>';
			$html[] = '<br />';
		}

		return implode(PHP_EOL, $html);
	}
	
	/**
	 * Generates an HTML check list
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function checklist( $config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'list' 		=> null,
			'name'   	=> 'id',
			'attribs'	=> array(),
			'key'		=> 'id',
			'text'		=> 'title',
			'selected'	=> null,
			'translate'	=> false
		));
		
		$name    = $config->name;
		$attribs = KHelperArray::toString($config->attribs);

		$html = array();
		foreach($config->list as $row)
		{
			$key  = $row->{$config->key};
			$text = $config->translate ? text( $row->{$config->text} ) : $row->{$config->text};
			$id	  = isset($row->id) ? $row->id : null;

			$extra = '';
			
			if ($config->selected instanceof KConfig)
			{
				foreach ($config->selected as $value)
				{
					$sel = is_object( $value ) ? $value->{$config->key} : $value;
					if ($key == $sel)
					{
						$extra .= 'checked="checked"';
						break;
					}
				}
			} 
			else $extra .= ($key == $config->selected) ? 'checked="checked"' : '';

			$html[] = '<input type="checkbox" name="'.$name.'[]" id="'.$name.$key.'" value="'.$key.'" '.$extra.' '.$attribs.' />';
			$html[] = '<label for="'.$name.$key.'">'.$text.'</label>';
		}

		return implode(PHP_EOL, $html);
	}
	
	/**
	 * Generates an HTML boolean radio list
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function booleanlist( $config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'name'   	=> '',
			'attribs'	=> array(),
			'true'		=> 'yes',
			'false'		=> 'no',
			'selected'	=> null,
			'translate'	=> true
		));
		
		$name    = $config->name;
		$attribs = KHelperArray::toString($config->attribs);
		
		$html  = array();
		
		$extra = !$config->selected ? 'checked="checked"' : '';
		$text  = $config->translate ? text( $config->false ) : $config->false;
		
		$html[] = '<label for="'.$name.'0">'.$text.'</label>';
		$html[] = '<input type="radio" name="'.$name.'" id="'.$name.'0" value="0" '.$extra.' '.$attribs.' />';	
		
		$extra = $config->selected ? 'checked="checked"' : '';
		$text  = $config->translate ? text( $config->true ) : $config->true;
		
		$html[] = '<label for="'.$name.'1">'.$text.'</label>';
		$html[] = '<input type="radio" name="'.$name.'" id="'.$name.'1" value="1" '.$extra.' '.$attribs.' />';	
		
		return implode(PHP_EOL, $html);
	}
}