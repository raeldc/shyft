<?php
/**
 * @version     $Id: response.php 4675 2012-06-03 01:05:49Z johanjanssens $
 * @package     Koowa_Http
 * @subpackage  Request
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Http Message Headers Class
 *
 * Container class that handles the aggregations of HTTP headers as a collection
 *
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Http
 * @subpackage  Request
 */
class KHttpResponseHeaders extends KHttpMessageHeaders
{
    /**
     * A list of response cookies
     *
     * @var array
     */
    protected $_cookies = array();

    /**
     * Add a cookie.
     *
     * @param Cookie $cookie
     */
    public function addCookie(KHttpCookie $cookie)
    {
        $this->_cookies[$cookie->domain][(string) $cookie->path][$cookie->name] = $cookie;
    }

    /**
     * Removes a cookie from the array, but does not unset it in the browser
     *
     * @param string $name
     * @param mixed  $path
     * @param string $domain
     */
    public function removeCookie($name, $path = '/', $domain = null)
    {
        if (null === $path) {
            $path = '/';
        }

        //Force to string to allow passing objects
        $path = (string) $path;

        unset($this->_cookies[$domain][$path][$name]);

        if (empty($this->_cookies[$domain][$path]))
        {
            unset($this->_cookies[$domain][$path]);

            if (empty($this->_cookies[$domain])) {
                unset($this->_cookies[$domain]);
            }
        }
    }

    /**
     * Clears a cookie in the browser
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     */
    public function clearCookie($name, $path = '/', $domain = null)
    {
        $cookie = $this->getService('koowa:http.cookie', array(
            'name'   => $name,
            'path'   => $path,
            'domain' => $domain,
        ));

        $this->addCookie($cookie);
    }

    /**
     * Returns an array with all cookies
     *
     * @return array
     */
    public function getCookies()
    {
        $result = array();
        foreach ($this->_cookies as $path)
        {
            foreach ($path as $cookies)
            {
                foreach ($cookies as $cookie) {
                    $result[] = $cookie;
                }
            }
        }

        return $result;
    }

    /**
     * Returns the headers as a string.
     *
     * @return string The headers
     */
    public function __toString()
    {
        $cookies = '';
        foreach ($this->getCookies() as $cookie) {
            $cookies .= 'Set-Cookie: '.$cookie."\r\n";
        }

        return parent::__toString().$cookies;
    }
}