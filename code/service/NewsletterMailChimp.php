<?php

class NewsletterMailChimp
{
    protected static $config;

    /**
     * Retrieve the config
     *
     * @return mixed
     */
    protected static function config()
    {
        if (!static::$config) {
            static::$config = SiteConfig::current_site_config();
        }

        return static::$config;
    }

    /**
     * Retrieve an instance of the mailchimp client
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

        if (!$config->NewsletterMailChimpApiKey || !$config->NewsletterMailChimpList) {
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

    public static function unsubscribe($hash)
    {
        $config = static::config();

        if (!$config->NewsletterMailChimpApiKey || !$config->NewsletterMailChimpList) {
            return false;
        }

        static::get_client()->unsubscribe($config->NewsletterMailChimpList, $hash);
    }
}