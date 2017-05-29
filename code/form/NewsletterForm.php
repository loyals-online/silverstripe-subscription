<?php

class NewsletterForm extends Form
{
    /**
     * ProspectForm constructor.
     *
     * @param \Page_Controller $controller
     * @param string           $name
     */
    public function __construct(\Page_Controller $controller, $name, $location = null)
    {
        parent::__construct($controller, $name, FieldList::create(), FieldList::create(), null);

        $this->fields    = $this->getFieldList($location);
        $this->actions   = $this->getActionList($location);
        $this->validator = $this->getRequiredFieldList();

        // setup form errors (re-init after parent::__construct call)
        parent::setupFormErrors();

        // trigger foundation abide validation
        $this->setAttribute('data-abide', 'abide');
    }

    /**
     * Retrieve the field list for this form
     *
     * @return \FieldList
     */
    protected function getFieldList($location = null)
    {
        return FieldList::create(
            CompositeField::create(
                CompositeField::create(
                    $this->getBareFieldList($location)
                )
            )
                ->addExtraClass('row')
        )
            ->setForm($this);
    }

    /**
     * Retrieve the actions for this form
     *
     * @return \FieldList
     */
    protected function getActionList($location = null)
    {
        return FieldList::create(
            $this->getBareActionList($location)
        )
            ->setForm($this);
    }

    /**
     * Retrieve the validator for this form
     *
     * @return RequiredFields
     */
    protected function getRequiredFieldList()
    {
        return RequiredFields::create(
            $this->getBareRequiredFieldList()
        )
            ->setForm($this);
    }

    /**
     * Retrieve the bare field list for this form
     *
     * @return array
     */
    protected function getBareFieldList($location = null)
    {
        $fields = [
            TextField::create(
                'Name',
                _t('NewsletterSubscription.db_Name', 'Name')
            ),
            EmailField::create(
                'Email',
                _t('NewsletterSubscription.db_Email', 'Email')
            ),
        ];

        if ($location == 'Footer') {
            /** @var FormField $field */
            foreach ($fields as $field) {
                $field->setFieldHolderTemplate('FormField_Half_holder');
            }
        }

        $this->extend('updateBareFieldList', $fields);

        return $fields;
    }

    /**
     * Retrieve the bare action list for this form
     *
     * @return \FieldList
     */
    protected function getBareActionList($location = null)
    {
        $actions = [
            FormAction::create(
                'process',
                _t('Newsletter.Form.Submit', 'Submit')
            )
                ->addExtraClass('medium secondary right')
                ->setUseButtonTag(true),
        ];

        $this->extend('updateBareActionList', $actions);

        return $actions;
    }

    /**
     * Retrieve the validator fields for this form
     *
     * @return static
     */
    protected function getBareRequiredFieldList()
    {
        $requiredFields = array_keys(singleton('NewsletterSubscription')->stat('db'));

        $this->extend('updateBareRequiredFieldList', $requiredFields);

        return $requiredFields;
    }

    /**
     * Process the request
     *
     * @param $data
     * @param $form
     *
     * @return bool|\SS_HTTPResponse
     */
    public function process($data, $form)
    {
        $jsend = new JSendResponse();
        $siteConfig = $this->controller->data()->alternateSiteConfig();

        // prevent duplicate key errors
        if (!($sub = NewsletterSubscription::get()
            ->filter(['Email' => $data['Email']])
            ->First())
        ) {

            $sub = NewsletterSubscription::create();
            $this->saveInto($sub);
            if ($sub->write()) {
                switch ($siteConfig->NewsletterSubscriptionService) {
                    case NewsletterSiteConfigExtension::SERVICE_MAILCHIMP:
                        if ($identifier = NewsletterMailChimp::subscribe($data)) {
                            $sub->Identifier = $identifier;
                            $sub->write();
                        } else {
                            $jsend->setStatus(JSendResponse::STATUS_FAIL);
                        }
                        break;
                }
            } else {
                $jsend->setStatus(JSendResponse::STATUS_ERROR);
                $jsend->setCode(500);
            }
        }

        if ($this->controller->getRequest()
            ->isAjax()
        ) {
            $jsend->setData([
                'response' => $jsend->isSuccess() ? $siteConfig->NewsletterThanksContent : $siteConfig->NewsletterErrorMessage,
            ]);

            $response = new SS_HTTPResponse($jsend->getJson());

            return $response;
        }

        Session::set('SubscriptionSaved', true);

        return $this->controller->redirectBack();
    }
}