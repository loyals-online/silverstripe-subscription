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
        return NewsletterForm::create($this->owner, 'NewsletterForm', $location);
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
