<?php

class NewsletterSubscription extends DataObject
{
    /**
     * @inheritdoc
     */
    private static $db = [
        'Name'       => 'Varchar',
        'Email'      => 'Varchar(254)',
        'Identifier' => 'Varchar',
    ];

    private static $indexes = [
        'Email' => [ 'type' => 'unique', 'value' => 'Email' ],
    ];

    /**
     * @inheritdoc
     */
    private static $summary_fields = [
        'Name',
        'Email',
    ];

    /**
     * @inheritdoc
     */
    public function canCreate($member = null)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function canEdit($member = null)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();

        $siteConfig = SiteConfig::current_site_config();
        switch ($siteConfig->NewsletterSubscriptionService) {
            case NewsletterSiteConfigExtension::SERVICE_MAILCHIMP:
                NewsletterMailChimp::unsubscribe($this->Identifier);
                break;
            case NewsletterSiteConfigExtension::SERVICE_GETRESPONSE:
                NewsletterGetResponse::unsubscribe($this->Identifier);
                break;
        }
    }
}