<?php
/**
 * @version		$Id$
 * @package		Koowa_Exception
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Exception Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Exception
 */
interface KExceptionInterface
{
	/**
	 * Return the exception message
	 *
	 * @return string
	 */
    public function getMessage();

    /**
	 * Return the user defined exception code
	 *
	 * @return integer
	 */
    public function getCode();

    /**
	 * Return the source filename
	 *
	 * @return string
	 */
    public function getFile();

    /**
	 * Return the source line number
	 *
	 * @return integer
	 */
    public function getLine();

    /**
	 * Return the backtrace information
	 *
	 * @return array
	 */
    public function getTrace();

    /**
	 * Return the backtrace as a string
	 *
	 * @return string
	 */
    public function getTraceAsString();
}