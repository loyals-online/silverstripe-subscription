<?php

/**
 * Created by PhpStorm.
 * User: mschenk
 * Date: 26/01/17
 * Time: 15:05
 */
class NewsletterPageControllerExtension extends Extension
{
    /**
     * @inheritdoc
     */
    private static $allowed_actions = [
        'NewsletterForm',
    ];

    /**
     * Newsletter form
     *
     * @return Form
     */
    public function NewsletterForm($location = null)
    {
        if (SiteConfig::current_site_config()->NewsletterSubscriptionService && SiteConfig::current_site_config()->NewsletterSubscriptionService <> NewsletterSiteConfigExtension::SERVICE_NONE) {
            return NewsletterForm::create($this->owner, 'NewsletterForm', $location);
        }

        return null;
    }

    /**
     * Was the newsletter subscription sent?
     *
     * @return bool
     */
    public function SubscriptionSaved()
    {
        if (Session::get('SubscriptionSaved')) {
            Session::clear('SubscriptionSaved');
            return true;
        }

        return false;
    }
}
