<?php

class MailChimp
{
    /**
     * Base url to MailChimp API v3
     *
     * @var string
     */
    protected static $base_url = '.api.mailchimp.com/3.0';

    /**
     * Selected end-point of the API
     *
     * @var string
     */
    protected $endPoint;

    /**
     * API key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * cURL handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * Constants Member Status
     *
     */
    const MEMBER_STATUS_SUBSCRIBED   = 'subscribed';
    const MEMBER_STATUS_UNSUBSCRIBED = 'unsubscribed';
    const MEMBER_STATUS_CLEANED      = 'cleaned';
    const MEMBER_STATUS_PENDING      = 'pending';

    /**
     * Constants Member Emailtype
     *
     */
    const MEMBER_EMAILTYPE_HTML = 'html';
    const MEMBER_EMAILTYPE_TEXT = 'text';

    /**
     * Create a new MailChimp client
     *
     * @param string $apiKey
     *
     * @return static
     */
    public static function create($apiKey)
    {
        return new static($apiKey);
    }

    /**
     * MailChimp constructor.
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $dc           = substr($apiKey, -3);

        $this->endPoint = sprintf('https://%1$s%2$s', $dc, static::$base_url);

        $this->handle = curl_init();
    }

    /**
     * Subscribe an emailaddress
     *
     * @param        $list
     * @param        $email
     * @param string $status
     * @param string $type
     *
     * @return mixed
     */
    public function subscribe($list, $email, $status = self::MEMBER_STATUS_SUBSCRIBED, $type = self::MEMBER_EMAILTYPE_HTML)
    {
        return $this->post(
            sprintf('/lists/%1$s/members', $list),
            [
                'email_type'    => $type,
                'status'        => $status,
                'email_address' => $email,
            ]
        );
    }

    /**
     * Send a post request to the API
     *
     * @param       $path
     * @param array $data
     *
     * @return mixed
     */
    protected function post($path, array $data)
    {
        $data = json_encode($data);

        $url  = sprintf('%1$s%2$s', $this->endPoint, $path);

        curl_setopt_array(
            $this->handle,
            [
                CURLOPT_URL        => $url,
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    sprintf('Content-Length: %1$d', strlen($data)),
                ],
            ]
        );

        return $this->perform();
    }

    /**
     * Perform a cURL request
     *
     * @return mixed
     */
    protected function perform()
    {
        curl_setopt_array(
            $this->handle,
            [
                CURLOPT_USERPWD        => sprintf('Mediaweb:%1$s', $this->apiKey),
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        $response = curl_exec($this->handle);

        curl_close($this->handle);

        if ($response) {
            $response = @json_decode($response);
        }
        // @todo error handling
        return $response;
    }
}