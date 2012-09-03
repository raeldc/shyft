<?php
/**
 * @version     $Id$
 * @package     Koowa_Http
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * HTTP Url Class
 *
 * This class helps you to create and manipulate urls, including query
 * strings and path elements. It does so by splitting up the pieces of the
 * url and allowing you modify them individually; you can then then fetch
 * them as a single url string. This helps when building complex links,
 * such as in a paged navigation system.
 *
 * The following is a simple example. Say that the page address is currently
 * `http://anonymous::guest@example.com/path/to/index.php/foo/bar?baz=dib#anchor`.
 *
 * You can use KHttpUrl to parse this complex string very easily:
 *
 * <code>
 * <?php
 *     // Create a url object;
 *
 *     $url = 'http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor'
 *     $url = KService::get('koowa:http.url', array('url' => $url) );
 *
 *     // the $ur properties are ...
 *     //
 *     // $url->scheme   => 'http'
 *     // $url->host     => 'example.com'
 *     // $url->user     => 'anonymous'
 *     // $url->pass     => 'guest'
 *     // $url->path     => array('path', 'to', 'index.php', 'foo', 'bar')
 *     // $url->format   => 'xml'
 *     // $url->query    => array('baz' => 'dib')
 *     // $url->fragment => 'anchor'
 * ?>
 * </code>
 *
 * Now that we have imported the url and had it parsed automatically, we
 * can modify the component parts, then fetch a new url string.
 *
 * <code>
 * <?php
 *     // change to 'https://'
 *     $url->scheme = 'https';
 *
 *     // remove the username and password
 *     $url->user = '';
 *     $url->pass = '';
 *
 *     // change the value of 'baz' to 'zab'
 *     $url->setQuery('baz', 'zab');
 *
 *     // add a new query element called 'zim' with a value of 'gir'
 *     $url->query['zim'] = 'gir';
 *
 *     // reset the path to something else entirely.
 *     // this will additionally set the format to 'php'.
 *     $url->setPath('/something/else/entirely.php');
 *
 *     // add another path element
 *     $url->path[] = 'another';
 *
 *     // and fetch it to a string.
 *     $new_url = $url->getUrl();
 *
 *     // the $new_url string is as follows; notice how the format
 *     // is always applied to the last path-element.
 *     // /something/else/entirely/another.php?baz=zab&zim=gir#anchor
 *
 *     // Get the full URL to get the scheme and host
 *     $full_url = $url->getUrl(true);
 *
 *     // the $full_url string is:
 *     // https://example.com/something/else/entirely/another.php?baz=zab&zim=gir#anchor
 * ?>
 * </code>
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Http
 */
class KHttpUrl extends KObject
{
    /**
     * The url parts
     *
     * @see get()
     */
    const SCHEME   = 1;
    const USER     = 2;
    const PASS     = 4;
    const HOST     = 8;
    const PORT     = 16;
    const PATH     = 32;
    const FORMAT   = 64;
    const QUERY    = 128;
    const FRAGMENT = 256;

    const AUTH = 6;
    const BASE = 127;
    const FULL = 511;

    /**
     * The scheme [http|https|ftp|mailto|...]
     *
     * @var string
     */
    public $scheme = '';

    /**
     * The host specification (for example, 'example.com').
     *
     * @var string
     */
    public $host = '';

    /**
     * The port number (for example, '80').
     *
     * @var string
     */
    public $port = '';

    /**
     * The username, if any.
     *
     * @var string
     */
    public $user = '';

    /**
     * The password, if any.
     *
     * @var string
     */
    public $pass = '';

    /**
     * The dot-format extension of the last path element (for example, the "rss"
     * in "feed.rss").
     *
     * @var string
     */
    public $format = '';

    /**
     * The fragment aka anchor portion (for example, the "foo" in "#foo").
     *
     * @var string
     */
    public $fragment = '';

    /**
     * The query portion (for example baz=dib)
     *
     * Public access is allowed via __get() with $query.
     *
     * @var array
     *
     * @see setQuery()
     * @see getQuery()
     */
    protected $_query = array();

    /**
     * The path portion (for example, 'path/to/index.php').
     *
     * @var string
     *
     * @see setPath()
     * @see getPath()
     */
    protected $_path = '';

    /**
     * Url-encode only these characters in path elements.
     *
     * Characters are ' ' (space), '/', '?', '&', and '#'.
     *
     * @var array
     */
    protected $_encode_path = array(
        ' ' => '+',
        '/' => '%2F',
        '?' => '%3F',
        '&' => '%26',
        '#' => '%23',
    );

    /**
     * Escape '&' to '&amp;'
     *
     * @var boolean
     *
     * @see getQuery()
     * @see getUrl()
     */
    protected $_escape;

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Set the escaping behavior
        $this->_escape = $config->escape;

        //Set the url from a string
        $this->setUrl($config->url);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation
     *
     * @param   KConfig $config An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'url' => '',
            'escape' => false
        ));

        parent::_initialize($config);
    }

    /**
     * Set the virtual properties.
     *
     * @param   string $key   The virtual property to set.
     * @param   string $value Set the virtual property to this value.
     */
    public function __set($key, $value)
    {
        if ($key == 'query') {
            $this->setQuery($value);
        }

        if ($key == 'path') {
            $this->setPath($value);
        }
    }

    /**
     * Get the virtual properties by reference so that they appears to be public
     *
     * @param   string  $key The virtual property to return.
     * @return  mixed   The value of the virtual property.
     */
    public function &__get($key)
    {
        if ($key == 'query') {
            return $this->_query;
        }

        if ($key == 'path') {
            return $this->_path;
        }

        return null;
    }

    /**
     * Get the full url, of the format scheme://user:pass@host/path?query#fragment';
     *
     * @param integer $parts A bitmask of binary or'ed HTTP_URL constants; FULL is the default
     * @return  string
     */
    public function getUrl($parts = self::FULL)
    {
        $url = '';

        //Add the scheme
        if (($parts & self::SCHEME) && !empty($this->scheme)) {
            $url .= urlencode($this->scheme) . '://';
        }

        //Add the username and password
        if (($parts & self::USER) && !empty($this->user))
        {
            $url .= urlencode($this->user);
            if (($parts & self::PASS) && !empty($this->pass)) {
                $url .= ':' . urlencode($this->pass);
            }

            $url .= '@';
        }

        // Add the host and port, if any.
        if (($parts & self::HOST) && !empty($this->host))
        {
            $url .= urlencode($this->host);

            if (($parts & self::PORT) && !empty($this->port)) {
                $url .= ':' . (int)$this->port;
            }
        }

        // Add the rest of the url. we use trim() instead of empty() on string
        // elements to allow for string-zero values.
        if (($parts & self::PATH) && !empty($this->_path))
        {
            $url .= $this->getPath();
            if (($parts & self::FORMAT) && trim($this->format) !== '') {
                $url .= '.' . urlencode($this->format);
            }
        }

        if (($parts & self::QUERY) && !empty($this->_query)) {
            $url .= '?' . $this->getQuery(false, $this->_escape);
        }

        if (($parts & self::FRAGMENT) && trim($this->fragment) !== '') {
            $url .= '#' . urlencode($this->fragment);
        }

        return $url;
    }

    /**
     * Set the url
     *
     * @param   string  $url
     * @return  KHttpUrl
     */
    public function setUrl($url)
    {
        if (!empty($url))
        {
            foreach (parse_url($url) as $key => $value) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Sets the query string in the url, for KHttpUrl::getQuery() and KHttpUrl::$query.
     *
     * This will overwrite any previous values.
     *
     * @param   string|array  $query  The query string to use; for example `foo=bar&baz=dib`.
     * @param   boolean       $merge  If TRUE the data in $query will be merged instead of replaced. Default FALSE.
     * @return  KHttpUrl
     */
    public function setQuery($query, $merge = false)
    {
        $result = $query;
        if (!is_array($query))
        {
            if (strpos($query, '&amp;') !== false) {
                $query = str_replace('&amp;', '&', $query);
            }

            //Set the query vars
            parse_str($query, $result);
        }

        if ($merge) {
            $this->_query = array_merge($this->_query, $result);
        } else {
            $this->_query = $result;
        }

        return $this;
    }

    /**
     * Returns the query portion as a string or array
     *
     * @param     boolean $toArray If TRUE return an array. Default FALSE
     * @param     boolean $escape  If TRUE escapes '&' to '&amp;' for xml compliance. Default FALSE
     * @return  string|array The query string; e.g., `foo=bar&baz=dib`.
     */
    public function getQuery($toArray = false, $escape = false)
    {
        $result = $toArray ? $this->_query : http_build_query($this->_query, '', $escape ? '&amp;' : '&');
        return $result;
    }

    /**
     * Sets the KHttpUrl::$path array and $format from a string.
     *
     * This will overwrite any previous values. Also, resets the format based
     * on the final path value.
     *
     * @param   string|array  $path The path string or array of elements to use; for example,"/foo/bar/baz/dib".
     *                              A leading slash will *not* create an empty first element; if the string has a
     *                              leading slash, it is ignored.
     * @return  KHttpUrl
     */
    public function setPath($path)
    {
        if (is_string($path))
        {
            if (!empty($path)) {
                $path = explode('/', $path);
            } else {
                $path = array();
            }
        }

        foreach ($path as $key => $val) {
            $path[$key] = urldecode($val);
        }

        if ($val = end($path))
        {
            // find the last dot in the value
            $pos = strrpos($val, '.');

            if ($pos !== false) {
                $key = key($path);
                $this->format = substr($val, $pos + 1);
                $path[$key] = substr($val, 0, $pos);
            }
        }

        $this->_path = $path;
        return $this;
    }

    /**
     * Returns the path portion as a string or array
     *
     * @param     boolean $toArray If TRUE return an array. Default FALSE
     * @return  string|array The path string; e.g., `path/to/site`.
     */
    public function getPath($toArray = false)
    {
        $result = $toArray ? $this->_path : $this->_pathEncode($this->_path);
        return $result;
    }

    /**
     * Return a string representation of this url.
     *
     * @see    get()
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl(self::FULL);
    }

    /**
     * Converts an array of path elements into a string.
     *
     * Does not use urlencode(); instead, only converts characters found in KHttpUrl::$_encode_path.
     *
     * @param  array $spec The path elements.
     * @return string A url path string.
     */
    protected function _pathEncode($spec)
    {
        if (is_string($spec)) {
            $spec = explode('/', $spec);
        }

        $keys = array_keys($this->_encode_path);
        $vals = array_values($this->_encode_path);

        $out = array();
        foreach ((array)$spec as $elem) {
            $out[] = str_replace($keys, $vals, $elem);
        }

        return implode('/', $out);
    }
}