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
}