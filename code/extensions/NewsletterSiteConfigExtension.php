<?php

class NewsletterSiteConfigExtension extends Extension
{
    /**
     * Const service
     */
    const SERVICE_MAILCHIMP   = 'MailChimp';
    const SERVICE_GETRESPONSE = 'GetResponse';

    /**
     * @inheritdoc
     */
    private static $db = [
        'NewsletterSubscriptionService' => 'Enum("MailChimp, GetResponse", "MailChimp")',
        'NewsletterMailChimpApiKey'     => 'Varchar',
        'NewsletterMailChimpList'       => 'Varchar',
        'NewsletterGetResponseApiKey'   => 'Varchar',
        'NewsletterGetResponseList'     => 'Varchar',
        'NewsletterThanksTitle'         => 'Varchar',
        'NewsletterErrorMessage'        => 'Text',
        'NewsletterThanksContent'       => 'CustomHTMLText',
    ];

    /**
     * @inheritdoc
     */
    public function updateCMSFields(FieldList $fields)
    {
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
            // MailChimp
            DisplayLogicWrapper::create(HeaderField::create(
                _t('Newsletter.MailChimp.ConfigTitle', 'MailChimp Configuration')
            ))
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo(static::SERVICE_MAILCHIMP)
                ->end(),
            TextField::create(
                'NewsletterMailChimpApiKey',
                _t('Newsletter.MailChimp.ApiKey', 'API Key')
            )
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo(static::SERVICE_MAILCHIMP)
                ->end(),
            $this->getListField(static::SERVICE_MAILCHIMP)
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo(static::SERVICE_MAILCHIMP)
                ->end(),
            // GetResponse
            DisplayLogicWrapper::create(HeaderField::create(
                _t('Newsletter.GetResponse.ConfigTitle', 'GetResponse Configuration')
            ))
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo(static::SERVICE_GETRESPONSE)
                ->end(),
            TextField::create(
                'NewsletterGetResponseApiKey',
                _t('Newsletter.GetResponse.ApiKey', 'API Key')
            )
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo(static::SERVICE_GETRESPONSE)
                ->end(),
            $this->getListField(static::SERVICE_GETRESPONSE)
                ->displayIf('NewsletterSubscriptionService')
                ->isEqualTo(static::SERVICE_GETRESPONSE)
                ->end(),

            HeaderField::create(
                _t('Newsletter.Thanks.ConfigTitle', 'Thanks page')
            ),
            TextField::create(
                'NewsletterThanksTitle',
                _t('Newsletter.Thanks.Title', 'Title')
            ),
            TextareaField::create(
                'NewsletterErrorMessage',
                _t('Newsletter.Error.Message', 'Error Message')
            )
                ->setRows(3),
            CustomHtmlEditorField::create(
                'NewsletterThanksContent',
                _t('Newsletter.Thanks.Content', 'Content')
            )
                ->setRows(15),
        ]);

        return $fields;
    }

    /**
     * Retrieve a field for the lists/campaigns
     *
     * @param string $service
     *
     * @return DropdownField|DisplayLogicWrapper
     */
    protected function getListField($service = self::SERVICE_MAILCHIMP)
    {
        switch ($service) {
            case static::SERVICE_MAILCHIMP:
                $lists = NewsletterMailChimp::get_lists();
                break;
            case static::SERVICE_GETRESPONSE:
                $lists = NewsletterGetResponse::get_lists();
                break;
            default:
                $lists = null;
                break;
        }

        return $lists
            ? DropdownField::create(
                sprintf('Newsletter%1$sList', $service),
                _t(sprintf('Newsletter.%1$s.List', $service), 'List'),
                $lists,
                $this->owner->{sprintf('Newsletter%1$sList', $service)}
            )
            : DisplayLogicWrapper::create(LabelField::create(
                sprintf('Newsletter%1$sList', $service),
                _t(sprintf('Newsletter.%1$s.NoApiKey', $service), 'Please enter the API key and save')
            ));
    }
}