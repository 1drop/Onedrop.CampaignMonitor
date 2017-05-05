<?php
namespace Onedrop\CampaignMonitor\Validation\Validator;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Validation\Validator\EmailAddressValidator;
use Onedrop\CampaignMonitor\Domain\Service\CampaignMonitorService;

/**
 * Validator for email addresses
 *
 * @api
 * @Flow\Scope("singleton")
 */
class UniqueSubscriptionValidator extends EmailAddressValidator
{

    /**
     * @Flow\Inject
     * @var CampaignMonitorService
     */
    protected $campaignMonitorService;

    /**
     * @var array
     */
    protected $supportedOptions = [
        'listId' => [null, 'CampaignMonitor List ID', 'string', true],
    ];

    /**
     * Checks if the given value is a valid email address.
     *
     * @param  mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        $options = $this->getOptions();
        if ($this->validEmail($value) && $this->campaignMonitorService->isMember($options['listId'], $value)) {
            $this->addError('This email address is already registered in our newsletter.', 1422317184);
        }
    }
}
