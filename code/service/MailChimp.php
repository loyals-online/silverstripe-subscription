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
        $dc           = substr($apiKey, strpos($apiKey, '-') + 1);

        $this->endPoint = sprintf('https://%1$s%2$s', $dc, static::$base_url);

        $this->handle = curl_init();
    }

    /**
     * Get the lists for this user
     *
     * @param int $offset
     *
     * @return mixed
     */
    public function getLists($offset = 0)
    {
        return $this->get(sprintf('/lists?offset=%1$d', $offset));
    }

    /**
     * Subscribe an emailaddress
     *
     * @param string $list
     * @param string $email
     * @param array  $data
     * @param string $status
     * @param string $type
     *
     * @return mixed
     */
    public function subscribe($list, $email, array $data = null, $status = self::MEMBER_STATUS_SUBSCRIBED, $type = self::MEMBER_EMAILTYPE_HTML)
    {
        $data = [
                'email_type'    => $type,
                'status'        => $status,
                'email_address' => $email,
                'language'      => i18n::get_lang_from_locale(i18n::get_locale()),
            ] + ($data ?: []);

        return $this->post(
            sprintf('/lists/%1$s/members', $list),
            $data
        );
    }

    /**
     * Unsubscribe a subscriber
     *
     * @param string $list
     * @param string $hash
     *
     * @return mixed
     */
    public function unsubscribe($list, $hash)
    {
        return $this->delete(
            sprintf('/lists/%1$s/members/%2$s', $list, $hash)
        );
    }

    /**
     * Send a get request to the API
     *
     * @param string $path
     *
     * @return mixed
     */
    protected function get($path)
    {
        $url = sprintf('%1$s%2$s', $this->endPoint, $path);

        curl_setopt_array(
            $this->handle, [
            CURLOPT_URL        => $url,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        return $this->perform();
    }

    /**
     * Send a post request to the API
     *
     * @param string $path
     * @param array  $data
     *
     * @return mixed
     */
    protected function post($path, array $data)
    {
        $data = json_encode($data);

        $url = sprintf('%1$s%2$s', $this->endPoint, $path);

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

    protected function delete($path)
    {
        $url = sprintf('%1$s%2$s', $this->endPoint, $path);

        curl_setopt_array(
            $this->handle, [
            CURLOPT_URL           => $url,
            CURLOPT_HTTPHEADER    => [
                'Content-Type: application/json',
            ],
            CURLOPT_CUSTOMREQUEST => 'DELETE',
        ]);

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