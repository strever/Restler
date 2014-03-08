<?php
namespace Luracast\Restler;

use Luracast\Restler\Format\JsonFormat;

/**
 * Static class for handling redirection
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc5
 */
class Redirect
{
    /**
     * Redirect to given url
     *
     * @param string $url relative path or full url
     * @param array $params associative array of query parameters
     * @param array $sessionData associative array of properties to be set in $_SESSION
     * @param int $status http status code to send the response with ideally 301 or 302
     *
     * @return array
     */
    public static function to($url, array $params = array(), array $sessionData = array(), $status = 302)
    {
        $url = ltrim($url, '/');
        /** @var $r Restler */
        $r = Scope::get('Restler');
        $base = $r->getBaseUrl() . '/';
        if (0 !== strpos($url, 'http'))
            $url = $base . $url;
        if (!empty($sessionData) || $base . $r->url !== $url || Util::getRequestMethod() != 'GET') {
            if ($r->responseFormat instanceof JsonFormat)
                return array('redirect' => $url);
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            foreach ($sessionData as $name => $value) {
                $_SESSION[$name] = $value;
            }
            header(
                "{$_SERVER['SERVER_PROTOCOL']} $status " .
                (isset(RestException::$codes[$status]) ? RestException::$codes[$status] : '')
            );
            header("Location: $url");
            die('');
        }
        return array();
    }
}