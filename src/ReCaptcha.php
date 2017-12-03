<?php

namespace Backtheweb\ReCaptcha;

use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class ReCaptcha {

    const GOOGLE_RECAPTCHA_CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    const GOOGLE_RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * The recaptcha secret key.
     *
     * @var string
     */
    protected $secret;

    /**
     * The recaptcha sitekey key.
     *
     * @var string
     */
    protected $key;

    /** @var array  */
    protected $attributes = [

        'data-sitekey'           => null,
        'data-theme'             => 'light',     // light, dark
        'data-size'              => 'normal',    // normal, compact
        'data-type'              => 'image',     // aduio, image
        'data-tabindex'          => 0,
        'data-callback'          => null,
        'data-expired-callback'  => null,
    ];

    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * GoogleCaptcha constructor.
     *
     * @param $secret
     * @param $key
     */
    public function __construct($secret, $key, Array $attributes = [])
    {
        $this->secret   = $secret;
        $this->key      = $key;
        $this->http     = new Client([ 'timeout' => 2.0 ]);

        $this->attributes['data-sitekey'] = $this->key;
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * Render HTML captcha.
     *
     * @param array  $attributes
     * @param string $lang
     *
     * @return string
     */
    public function display($attributes = [], $lang = null)
    {
        return $this->getBlockScript($lang) . $this->getBlockHtml($attributes);
    }

    /**
     * @param null $onload callback function name
     * @param null $render explicit | onload
     * @param null $lang
     * @return string
     */
    public function getBlockScript($onLoad = null, $render = 'onload', $lang = null)
    {
        if(null === $lang){

            $lang = \Locale::getPrimaryLanguage(App()->getLocale());
        }

        $q = http_build_query([
            'onload' => $onLoad,
            'render' => $render,
            'lang'   => $lang,
        ]);

        return '<script src="' . self::GOOGLE_RECAPTCHA_CLIENT_API  . '?' . $q . '" async defer></script>';
    }

    public function getBlockHtml(Array $attributes = [])
    {
        $attributes = array_merge($this->attributes, $attributes);

        return '<div class="g-recaptcha"' . $this->buildAttributes($attributes) . '></div>';
    }

    /**
     * Verify no-captcha response.
     *
     * @param string $response
     * @param string $clientIp
     *
     * @return bool
     */
    public function verifyResponse($response, $clientIp = null)
    {
        if (empty($response)) {
            return false;
        }

        $output = $this->sendRequestVerify([
            'secret'   => $this->secret,
            'response' => $response,
            'remoteip' => $clientIp,
        ]);

       var_dump($output); exit;

        return isset($output['success']) && $output['success'] === true;
    }

    /**
     * Verify no-captcha response by Symfony Request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function verifyRequest(Request $request)
    {
        return $this->verifyResponse(
            $request->get('g-recaptcha-response'),
            $request->getClientIp()
        );
    }

    /**
     * Get recaptcha js link.
     *
     * @param string $lang
     *
     * @return string
     */
    public function getJsLink($lang = null)
    {

    //onload=onloadCallback&render=explicit
        return $lang ? self::GOOGLE_RECAPTCHA_CLIENT_API . '?hl=' . $lang : self::GOOGLE_RECAPTCHA_CLIENT_API;
    }

    /**
     * Send verify request.
     *
     * @param array $query
     *
     * @return array
     */
    protected function sendRequestVerify(array $query = [])
    {
        $response = $this->http->request('POST', self::GOOGLE_RECAPTCHA_VERIFY_URL, [
            'form_params' => $query,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Build HTML attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    protected function buildAttributes(array $attributes)
    {
        $html = [];
        foreach ($attributes as $key => $value) {

            $html[] = $key . '="' . $value . '"';
        }
        return count($html) ? ' '.implode(' ', $html) : '';
    }
}