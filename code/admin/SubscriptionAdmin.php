<?php

class SubscriptionAdmin extends ModelAdmin
{
    /**
     * @inheritdoc
     */
    private static $managed_models = [
        'NewsletterSubscription',
    ];

    /**
     * @inheritdoc
     */
    private static $url_segment = 'subscriptions';

    /**
     * @inheritdoc
     */
    private static $menu_title  = 'Subscriptions';
}