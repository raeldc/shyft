<?php
/**
 * @version     $Id$
 * @package     Koowa_Http
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * HTTP Response Class
 *
 * @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Http
 */
class KHttpResponse extends KHttpMessageAbstract implements KHttpResponseInterface
{
    /**
     * The response status code
     *
     * @var int Status code
     */
    protected $_status_code;

    /**
     * The response status message
     *
     * @var int Status code
     */
    protected $_status_message;

    /**
     * The response content type
     *
     * @var int Status code
     */
    protected $_content_type;

    // [Successful 2xx]
    const OK                        = 200;  
    const CREATED                   = 201;  
    const ACCEPTED                  = 202;   
    const NO_CONTENT                = 204;  
    const RESET_CONTENT             = 205;  
    const PARTIAL_CONTENT           = 206;  
    
    // [Redirection 3xx]  
    const MOVED_PERMANENTLY         = 301;  
    const FOUND                     = 302;  
    const SEE_OTHER                 = 303;  
    const NOT_MODIFIED              = 304;  
    const USE_PROXY                 = 305;  
    const TEMPORARY_REDIRECT        = 307;  
    
    // [Client Error 4xx]  
    const BAD_REQUEST               = 400;  
    const UNAUTHORIZED              = 401;  
    const FORBIDDEN                 = 403;  
    const NOT_FOUND                 = 404;  
    const METHOD_NOT_ALLOWED        = 405;  
    const NOT_ACCEPTABLE            = 406;  
    const REQUEST_TIMEOUT           = 408;  
    const CONFLICT                  = 409;  
    const GONE                      = 410;  
    const LENGTH_REQUIRED           = 411;  
    const PRECONDITION_FAILED       = 412;  
    const REQUEST_ENTITY_TOO_LARGE  = 413;  
    const REQUEST_URI_TOO_LONG      = 414;  
    const UNSUPPORTED_MEDIA_TYPE    = 415;  
    const EXPECTATION_FAILED        = 417;  
    
    // [Server Error 5xx]  
    const INTERNAL_SERVER_ERROR     = 500;  
    const NOT_IMPLEMENTED           = 501;  
    const BAD_GATEWAY               = 502;  
    const SERVICE_UNAVAILABLE       = 503;  
    const GATEWAY_TIMEOUT           = 504;  
    const VERSION_NOT_SUPPORTED     = 505;

    /**
     * Status codes translation table.
     *
     * The list of codes is complete according to the
     * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol (HTTP) Status Code Registry}
     * (last updated 2012-02-13).
     *
     * Unless otherwise noted, the status code is defined in RFC2616.
     *
     * @var array
     */
    protected static $_status_messages = array(

        // [Successful 2xx]
        200 => 'OK',  
        201 => 'Created',  
        202 => 'Accepted', 
        204 => 'No Content',  
        205 => 'Reset Content',  
        206 => 'Partial Content',  

        // [Redirection 3xx]  
        300 => 'Multiple Choices',  
        301 => 'Moved Permanently',  
        302 => 'Found',  
        303 => 'See Other',  
        304 => 'Not Modified',  
        305 => 'Use Proxy',  
        307 => 'Temporary Redirect',  
        
        // [Client Error 4xx]  
        400 => 'Bad Request',  
        401 => 'Unauthorized',  
        403 => 'Forbidden',  
        404 => 'Not Found',  
        405 => 'Method Not Allowed',  
        406 => 'Not Acceptable',  
        408 => 'Request Timeout',  
        409 => 'Conflict',  
        410 => 'Gone',  
        411 => 'Length Required',  
        412 => 'Precondition Failed',  
        413 => 'Request Entity Too Large',  
        414 => 'Request-URI Too Long',  
        415 => 'Unsupported Media Type',  
        416 => 'Requested Range Not Satisfiable',  
        417 => 'Expectation Failed',  
        
        // [Server Error 5xx]  
        500 => 'Internal Server Error',  
        501 => 'Not Implemented',  
        502 => 'Bad Gateway',  
        503 => 'Service Unavailable',  
        504 => 'Gateway Timeout',  
        505 => 'HTTP Version Not Supported'  
    );

    /**
     * Constructor
     *
     * @param KConfig|null $config  An optional KConfig object with configuration options
     * @return \KHttpResponse
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->setContent($config->content);
        $this->setContentType($config->content_type);
        $this->setStatus($config->status_code);

        if (!$this->headers->has('Date')) {
            $this->setDate(new \DateTime(null, new \DateTimeZone('UTC')));
        }
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'content'      => '',
            'content_type' => 'text/html',
            'status_code'  => '200',
            'headers'      => $this->getService('koowa:http.response.headers')
        ));

        parent::_initialize($config);
    }

    /**
     * Set HTTP status code and (optionally) message
     *
     * @param  integer $code
     * @param  string $message
     * @throws InvalidArgumentException
     * @return KHttpResponse
     */
    public function setStatus($code, $message = null)
    {
        if (!is_numeric($code) || !isset(self::$_status_messages[$code]))
        {
            $code = is_scalar($code) ? $code : gettype($code);
            throw new InvalidArgumentException(sprintf(
                'Invalid status code provided: "%s"',
                $code
            ));
        }

        $this->_status_code    = (int) $code;
        $this->_status_message = trim($message);
        return $this;
    }

    /**
     * Retrieve HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_status_code;
    }

    /**
     * Get the http header status message based on a status code
     *
     * @return string The http status message
     */
    public function getStatusMessage()
    {
        $code = $this->getStatusCode();

        if (isset($this->_status_message)) {
            $message = self::$_status_messages[$code];
        } else {
            $message = $this->_status_message;
        }

        return $message;
    }

    /**
     * Sets the response content type
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
     *
     * @param string $type Content type
     * @return KHttpResponse
     */
    public function setContentType($type)
    {
        $this->_content_type = $type;
        $this->headers->set('Content-Type', array($type, 'charset' => 'utf-8'));

        return $this;
    }

    /**
     * Retrieves the response content type
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
     *
     * @return string Character set
     */
    public function getContentType()
    {
        return $this->_content_type;
    }

    /**
     * Sets a redirect
     *
     * @see http://tools.ietf.org/html/rfc2616#section-10.3
     *
     * @param string $url     The redirect location
     * @param  integer $code  The redirect status code
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @return KHttpResponse
     */
    public function setRedirect($location, $code = KHttpResponse::SEE_OTHER)
    {
        if (empty($location)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        if (300 >= $code && $code < 400) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $code));
        }

        if (!is_string($location) && !is_numeric($location) && !is_callable(array($location, '__toString'))) {
            throw new \UnexpectedValueException(
                'The Response location must be a string or object implementing __toString(), "'.gettype($location).'" given.'
            );
        }

        $this->setContent(sprintf(
            '<!DOCTYPE html>
                <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <meta http-equiv="refresh" content="1;url=%1$s" />
                        <title>Redirecting to %1$s</title>
                    </head>
                    <body>
                        Redirecting to <a href="%1$s">%1$s</a>.
                    </body>
                </html>'
            , htmlspecialchars($location, ENT_QUOTES, 'UTF-8')
         ));

        $this->setStatus($code);
        $this->headers->set('Location', (string) $location);

        return $this;
    }

    /**
     * Returns the Date header as a DateTime instance.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.18
     *
     * @return \DateTime A \DateTime instance
     * @throws \RuntimeException When the header is not parseable
     */
    public function getDate()
    {
        $date = new \DateTime();

        if ($this->headers->has('Date'))
        {
            $value = $this->headers->get('Date');

            if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
                throw new \RuntimeException(sprintf('The Last-Modified HTTP header is not parseable (%s).', $value));
            }
        }

        return $date;
    }

    /**
     * Sets the Date header.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.18
     *
     * @param \DateTime $date A \DateTime instance
     * @return KHttpResponse
     */
    public function setDate(\DateTime $date)
    {
        $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers->set('Date', $date->format('D, d M Y H:i:s').' GMT');

        return $this;
    }

    /**
     * Returns the Last-Modified HTTP header as a DateTime instance.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.29
     *
     * @return \DateTime A DateTime instance
     */
    public function getLastModified()
    {
        $date = null;

        if ($this->headers->has('Last-Modified'))
        {
           $value = $this->headers->get('Last-Modified');

            if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
                throw new \RuntimeException(sprintf('The Last-Modified HTTP header is not parseable (%s).', $value));
            }
        }

        return $date;
    }

    /**
     * Sets the Last-Modified HTTP header with a DateTime instance.
     *
     * If passed a null value, it removes the header.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.29
     *
     * @param \DateTime $date A \DateTime instance
     * @return Response
     */
    public function setLastModified(\DateTime $date = null)
    {
        if ($date !== null)
        {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s').' GMT');
        }
        else $this->headers->remove('Last-Modified');

        return $this;
    }

    /**
     * Returns the value of the Expires header as a DateTime instance.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21
     *
     * @return \DateTime A DateTime instance
     */
    public function getExpires()
    {
        $date = null;

        if ($this->headers->has('Expires'))
        {
            $value = $this->headers->get('Expires');

            if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
                throw new \RuntimeException(sprintf('The Expires HTTP header is not parseable (%s).', $value));
            }
        }

        return $date;
    }

    /**
     * Sets the Expires HTTP header with a DateTime instance.
     *
     * If passed a null value, it removes the header.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21
     *
     * @param \DateTime $date A \DateTime instance
     * @return KHttpResponse
     */
    public function setExpires(\DateTime $date = null)
    {
        if (null !== $date)
        {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Expires', $date->format('D, d M Y H:i:s').' GMT');

        } else $this->headers->remove('Expires');

        return $this;
    }

    /**
     * Returns the literal value of the ETag HTTP header.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.19
     *
     * @return string The ETag HTTP header
     */
    public function getEtag()
    {
        return $this->headers->get('ETag');
    }

    /**
     * Sets the ETag value.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.19
     *
     * @param string  $etag The ETag unique identifier
     * @param Boolean $weak Whether you want a weak ETag or not
     * @return KHttpResponse
     */
    public function setEtag($etag = null, $weak = false)
    {
        if (null !== $etag)
        {
            if (0 !== strpos($etag, '"')) {
                $etag = '"'.$etag.'"';
            }

            $this->headers->set('ETag', (true === $weak ? 'W/' : '').$etag);
        }
        else  $this->headers->remove('Etag');

        return $this;
    }

    /**
     * Returns the age of the response.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.6
     * @return integer The age of the response in seconds
     */
    public function getAge()
    {
        if ($age = $this->headers->get('Age')) {
            return $age;
        }

        return max(time() - $this->getDate()->format('U'), 0);
    }

    /**
     * Sets the number of seconds after the time specified in the response's Date header when the the response
     * should no longer be considered fresh.
     *
     * Uses the expires header to calculate the maximum age. It returns null when no max age can be established.
     *
     * @return integer|null Number of seconds
     */
    public function getMaxAge()
    {
        if ($this->getExpires() !== null) {
            return $this->getExpires()->format('U') - $this->getDate()->format('U');
        }

        return null;
    }

    /**
     * Is the response invalid
     *
     * @return Boolean
     */
    public function isInvalid()
    {
        return $this->_status_code < 100 || $this->_status_code >= 600;
    }


    /**
     * Check if an http status code is an error
     *
     * @return boolean TRUE if the status code is an error code
     */
    public function isError()
    {
        return ($this->getStatusCode() >= 400);
    }

    /**
     * Do we have a redirect
     *
     * @return bool
     */
    public function isRedirect()
    {
        $code = $this->getStatusCode();
        return (300 <= $code && 400 > $code);
    }

    /**
     * Was the response successful
     *
     * @return bool
     */
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        return (200 <= $code && 300 > $code);
    }

    /**
     * Returns true if the response includes headers that can be used to validate the response with the origin
     * server using a conditional GET request.
     *
     * @return Boolean true if the response is validateable, false otherwise
     */
    public function isValidateable()
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }

    /**
     * Returns true if the response is worth caching under any circumstance.
     *
     * Responses with that are stale (Expired) or without cache validation (Last-Modified, ETag) heades are
     * considered uncacheable.
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9.1
     * @return Boolean true if the response is worth caching, false otherwise
     */
    public function isCacheable()
    {
        if (!in_array($this->_status_code, array(200, 203, 300, 301, 302, 404, 410))) {
            return false;
        }

        $cache_control = (array) $this->headers->get('Cache-Control', null, false);
        if (isset($cache_control['no-store']) || isset($cache_control['no-cache'])) {
            return false;
        }

        return $this->isValidateable() || !$this->isStale();
    }

    /**
     * Returns true if the response is "stale".
     *
     * When the responses is stale, the response may not be served from cache without first re-validating with
     * the origin.
     *
     * @return Boolean true if the response is stale, false otherwise
     */
    public function isStale()
    {
        $result = true;
        if ($maxAge = $this->getMaxAge()) {
            $result = ($maxAge - $this->getAge()) <= O;
        }

        return $result;
    }

    /**
     * Render entire response as HTTP response string
     *
     * @return string
     */
    public function __toString()
    {
        $status = sprintf('HTTP/%s %d %s', $this->getVersion(), $this->getStatusCode(), $this->getStatusMessage());

        $str  = trim($status) . "\r\n";
        $str .= $this->headers;
        $str .= "\r\n";
        $str .= $this->getContent();
        return $str;
    }
}