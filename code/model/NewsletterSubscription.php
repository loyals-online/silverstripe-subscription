<?php

class NewsletterSubscription extends DataObject
{
    /**
     * @inheritdoc
     */
    private static $db = [
        'Name'  => 'Varchar',
        'Email' => 'Varchar(254)',
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
    public function canDelete($member = null)
    {
        return false;
    }

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
}