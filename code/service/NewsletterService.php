<?php

abstract class NewsletterService
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
}