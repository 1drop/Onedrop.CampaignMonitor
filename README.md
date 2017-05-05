Onedrop.CampaignMonitor
=======================

Package that integrates [CampaignMonitor](https://www.campaignmonitor.com/)® to your [Neos](https://www.neos.io) site or [Flow](https://flow.neos.io) application.

This package is heavily inspired by the [Neos Mailchimp package](https://github.com/bwaidelich/Wwwision.Neos.MailChimp).

Features
--------

This package comes with two main features:

1. A *CampaignMonitor® subscription finisher* for the [Flow Form Framework](https://flow-form-framework.readthedocs.io/en/stable/)
2. A simple *Neos* module that allows Neos administrators to manage CampaignMonitor® lists and recipients

Usage
-----

Install this package: `composer require onedrop/campaignmonitor`

After successful installation make sure to configure the CampaignMonitor® API key and the client ID in the `Settings.yaml`of your Site package:

```yaml
Onedrop:
  CampaignMonitor:
    apiKey: '<VALID_CAMPAIGNMONITOR_API_KEY>'
    clientID: '<VALID_CAMPAIGNMONITOR_CLIENT_ID>'
```

**Note:** The API key can be obtained from `Account > API Keys`

Done. You can now log-in to the Neos backend (as administrator) and manage your newsletter lists and recipients in the new Module `administration/campaignmonitor` (Make sure to flush the browser caches if the module should not appear in the menu).

Neos Module
-----------

The module is pretty simple and self-explanatory. Currently it allows for:

1. Displaying all lists
2. Displaying details of single lists including creation date, sender information, number of recipients
3. Displaying all members of a selected list
4. Removing members from a list
5. Subscribing new members to a list

Form Finisher
-------------

This package also comes with a simple form finisher that allows for creation of simple Newsletter subscription forms using the *Flow Form Framework*.
It also adds the corresponding *FormBuilder* configuration so that the finisher can be used directly in the visual editor.

Alternatively you can save the following snippet to `Data/Forms/newsletter.yaml` to create a simple newsletter subscription form:

```yaml
type: 'Neos.Form:Form'
identifier: campaignmonitor
label: Campaignmonitor
renderables:
    -
        type: 'Neos.Form:Page'
        identifier: page1
        label: 'Page 1'
        renderables:
            -
                type: 'Neos.Form:SingleLineText'
                identifier: 'firstName'
                label: 'First name'
                validators:
                    -
                        identifier: 'Neos.Flow:NotEmpty'
                properties:
                    placeholder: 'Your first name'
                defaultValue: ''
            -
                type: 'Neos.Form:SingleLineText'
                identifier: 'lastName'
                label: 'Last name'
                validators:
                    -
                        identifier: 'Neos.Flow:NotEmpty'
                properties:
                    placeholder: 'Your last name'
                defaultValue: ''
            -
                type: 'Neos.Form:SingleLineText'
                identifier: 'email'
                label: 'E-Mail'
                validators:
                    -
                        identifier: 'Neos.Flow:NotEmpty'
                    -
                        identifier: 'Neos.Flow:EmailAddress'
                    -
                        identifier: 'Onedrop.CampaignMonitor:UniqueSubscription'
                        options:
                          listId: '<CAMPAIGNMONITOR-LIST-ID>'
                properties:
                    placeholder: 'Your email address'
                defaultValue: ''
finishers:
    -
        identifier: 'Onedrop.CampaignMonitor:CampaignMonitorSubscriptionFinisher'
        options:
            listId: '<CAMPAIGNMONITOR-LIST-ID>'
            name: '{firstName} {lastName}'
            additionalFields:
              'salutation': '{salutation}'
    -
        identifier: 'Neos.Form:Confirmation'
        options:
            message: 'Thank you, your subscription was successful. Please check your email.'
renderingOptions:
    submitButtonLabel: ''
```

**Note:** Replace the two "\<CAMPAIGNMONITOR-LIST-ID\>" with a valid list identifier that can be obtained from `Lists & Subscribers > <YOUR-LIST> > change name/type > API Subscriber List ID`. A list ID usually contains letters and numbers such as "cbd1eb6f213f838b5a74e57ec8a19cef".

The Form finisher can of course be used without Neos (i.e. for Newsletter-subscriptions within plain Flow applications).

License
-------

Licensed under GPLv3+, see [LICENSE](LICENSE)
