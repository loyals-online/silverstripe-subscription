<?php

class NewsletterMailChimp extends NewsletterService
{
    /**
     * Retrieve an instance of the MailChimp client
     *
     * @return MailChimp
     */
    protected static function get_client()
    {
        $config = static::config();

        return MailChimp::create($config->NewsletterMailChimpApiKey);
    }

    /**
     * Retrieve MailChimp lists
     *
     * @return mixed
     */
    public static function get_lists()
    {
        $config = static::config();

        if (!$config->NewsletterMailChimpApiKey) {
            return false;
        }

        $response = static::get_client()
            ->getLists();
        if (isset($response->lists) && count($response->lists)) {
            $lists = [];
            foreach ($response->lists as $list) {
                $lists[$list->id] = $list->name;
            }

            return $lists;
        }

        return false;
    }

    /**
     * Process data and perform a request to the MailChimp API
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
                $config->NewsletterMailChimpList,
                $data['Email'],
                [
                    'merge_fields' => ['FNAME' => $data['Name']]
                ]
            );

        if (isset($response->id)) {
            return $response->id;
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
        $config = static::config();

        if (!static::canPerform()) {
            return false;
        }

        static::get_client()->unsubscribe($config->NewsletterMailChimpList, $hash);
    }

    /**
     * Can we perform
     *
     * @return bool
     */
    protected static function canPerform()
    {
        $config = static::config();

        return $config->NewsletterMailChimpApiKey && $config->NewsletterMailChimpList;
    }
}