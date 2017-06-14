<?php

class GetResponse
{
    /**
     * Base url to GetResponse API v3
     *
     * @var string
     */
    protected $base_url = 'https://api.getresponse.com/v3';

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
     * Create a new GetResponse client
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
     * GetResponse constructor.
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;

        $this->handle = curl_init();
    }

    /**
     * Retrieve the campaigns
     *
     * @return mixed
     */
    public function getCampaigns()
    {
        return $this->get('/campaigns');
    }

    /**
     * Subscribe an emailadress
     *
     * @param string     $email
     * @param string     $name
     * @param string     $campaignId
     * @param array|null $values
     *
     * @return mixed
     */
    public function subscribe($email, $name, $campaignId, array $values = null)
    {
        $data = [
            'name'     => $name,
            'email'    => $email,
            'campaign' => [
                'campaignId' => $campaignId,
            ],
        ];

        if ($values) {
            $newValues = [];

            foreach ($values as $key => $value) {
                array_push($newValues, [
                    'customFieldId' => $key,
                    'value'         => is_array($value) ? $value : [$value],
                ]);
            }

            $data['customFieldValues'] = $newValues;
        }

        return $this->post('/contacts', json_encode($data));
    }

    /**
     * Unsubscribe a subscriber
     *
     * @param string $identifier
     *
     * @return mixed
     */
    public function unsubscribe($identifier)
    {
        return $this->delete(sprintf('/contacts/%1$s', $identifier));
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
        $url = sprintf('%1$s%2$s', $this->base_url, $path);

        curl_setopt_array(
            $this->handle, [
            CURLOPT_URL        => $url,
            CURLOPT_HTTPHEADER => [
                $this->getAuthenticationHeader(),
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
    protected function post($path, $data)
    {
        $data = json_encode($data);

        $url = sprintf('%1$s%2$s', $this->base_url, $path);

        curl_setopt_array(
            $this->handle,
            [
                CURLOPT_URL        => $url,
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    $this->getAuthenticationHeader(),
                    'Content-Type: application/json',
                    sprintf('Content-Length: %1$d', strlen($data)),
                ],
            ]
        );

        return $this->perform();
    }

    /**
     * Send a delete request to the API
     *
     * @param string $path
     *
     * @return mixed
     */
    protected function delete($path)
    {
        $url = sprintf('%1$s%2$s', $this->base_url, $path);

        curl_setopt_array(
            $this->handle, [
            CURLOPT_URL           => $url,
            CURLOPT_HTTPHEADER    => [
                $this->getAuthenticationHeader(),
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
        curl_setopt(
            $this->handle,
            CURLOPT_RETURNTRANSFER,
            true
        );

        $response = curl_exec($this->handle);

        curl_close($this->handle);

        if ($response) {
            $response = @json_decode($response);
        }

        // @todo error handling
        return $response;
    }

    /**
     * Retrieve the authentication header
     *
     * @return string
     */
    protected function getAuthenticationHeader()
    {
        return sprintf('X-Auth-Token: api-key %1$s', $this->apiKey);
    }
}