<?php

class NewsletterSiteConfigExtension extends Extension
{
    /**
     * Const service
     */
    const SERVICE_MAILCHIMP = 'MailChimp';
    /**
     * @inheritdoc
     */
    private static $db = [
        'NewsletterSubscriptionService' => 'Enum("MailChimp", "MailChimp")',
        'NewsletterMailChimpApiKey'     => 'Varchar',
        'NewsletterMailChimpList'       => 'Varchar',
        'NewsletterThanksTitle'         => 'Varchar',
        'NewsletterThanksContent'       => 'CustomHTMLText',
    ];

    /**
     * @inheritdoc
     */
    public function updateCMSFields(FieldList $fields)
    {
        $lists = NewsletterMailChimp::get_lists();
        $dropdown = $lists
            ? DropdownField::create(
                'NewsletterMailChimpList',
                _t('Newsletter.MailChimp.List', 'List'),
                $lists,
                $this->owner->NewsletterMailChimpList
            )
            : LabelField::create(
            'NewsletterMailChimpList',
            _t('Newsletter.MailChimp.NoApiKey', 'Please enter the API key and save')
        );
        $fields->addFieldsToTab("Root.Newsletter", [
            HeaderField::create(
                _t('Newsletter.ConfigTitle', 'Newsletter configuration')
            ),
            DropdownField::create(
                'NewsletterSubscriptionService',
                _t('Newsletter.SubscriptionService', 'Subscription Service'),
                $this->owner->dbObject('NewsletterSubscriptionService')
                    ->enumValues()
            ),
            HeaderField::create(
                _t('Newsletter.MailChimp.ConfigTitle', 'MailChimp Configuration')
            )
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo('MailChimp')
                ->end(),
            TextField::create(
                'NewsletterMailChimpApiKey',
                _t('Newsletter.MailChimp.ApiKey', 'API Key')
            )
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo('MailChimp')
                ->end(),
            $dropdown
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo('MailChimp')
                ->end(),
            HeaderField::create(
                _t('Newsletter.Thanks.ConfigTitle', 'Thanks page')
            ),
            TextField::create(
                'NewsletterThanksTitle',
                _t('Newsletter.Thanks.Title', 'Title')
            ),
            CustomHtmlEditorField::create(
                'NewsletterThanksContent',
                _t('Newsletter.Thanks.Content', 'Content')
            )
                ->setRows(15),
        ]);

        return $fields;
    }

}