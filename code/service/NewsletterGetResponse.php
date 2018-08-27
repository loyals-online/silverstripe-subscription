<?php

class NewsletterGetResponse extends NewsletterService
{
    /**
     * Retrieve an instance of the GetResponse client
     *
     * @return GetResponse
     */
    protected static function get_client()
    {
        $config = static::config();

        return GetResponse::create($config->NewsletterGetResponseApiKey);
    }

    /**
     * Retrieve GetResponse lists
     *
     * @return mixed
     */
    public static function get_lists()
    {
        $config = static::config();

        if (!$config->NewsletterGetResponseApiKey) {
            return false;
        }

        $response = static::get_client()
            ->getCampaigns();

        if (count($response)) {
            $lists = [];
            foreach ($response as $list) {
                if (isset($list->campaignId, $list->name)) {
                    $lists[$list->campaignId] = $list->name;
                }
            }

            return $lists;
        }

        return false;
    }

    /**
     * Process data and perform a request to the GetResponse API
     *
     * @param $data
     *
     * @return mixed
     */
    public static function subscribe($data)
    {
        $config = static::config();

        if (!static::canPerform()) {
            return false;
        }

        $response = static::get_client()
            ->subscribe(
                $data['Email'],
                $data['Name'],
                $config->NewsletterGetResponseList
            );

        return (bool) $response;
    }

    public static function search($email)
    {
        if (!static::canPerform()) {
            return false;
        }

        $result = static::get_client()->search($email);

        if (count($result)) {
            return $result[0]->contactId;
        }

        return false;

    }

    /**
     * Unsubscribe a subscriber
     *
     * @param $hash
     *
     * @return bool
     */
    public static function unsubscribe($hash)
    {
        if (!static::canPerform()) {
            return false;
        }

        static::get_client()->unsubscribe($hash);
    }

    /**
     * Can we perform
     *
     * @return bool
     */
    protected static function canPerform()
    {
        $config = static::config();

        return $config->NewsletterGetResponseApiKey && $config->NewsletterGetResponseList;
    }
}