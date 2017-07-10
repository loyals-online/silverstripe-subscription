<?php

class NewsletterPage extends Page
{
}

class NewsletterPage_Controller extends Page_Controller
{
    /**
     * @inheritdoc
     */
    private static $allowed_actions = [
        'thanks'
    ];

    /**
     * Thanks action
     *
     * @return array
     */
    public function thanks()
    {
        $this->SubscriptionSaved();

        return [];
    }
}