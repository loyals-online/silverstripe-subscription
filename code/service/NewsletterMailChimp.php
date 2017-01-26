<?php

class NewsletterMailChimp
{
    /**
     * Process data and perform a request to the MailChimp API
     *
     * @param $data
     */
    public static function process($data)
    {
        $siteConfig = SiteConfig::current_site_config();

        if (!$siteConfig->NewsletterMailChimpApiKey || !$siteConfig->NewsletterMailChimpList) {
            return;
        }

        $chimp = MailChimp::create($siteConfig->NewsletterMailChimpApiKey);
        $chimp->subscribe($siteConfig->NewsletterMailChimpList, $data['Email']);
    }
}