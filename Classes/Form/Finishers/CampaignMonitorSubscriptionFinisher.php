<?php
namespace Onedrop\CampaignMonitor\Form\Finishers;

use Neos\Flow\Annotations as Flow;
use Neos\Utility\ObjectAccess;
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Exception\FinisherException;
use Onedrop\CampaignMonitor\Domain\Service\CampaignMonitorService;
use Onedrop\CampaignMonitor\Exception\CampaignMonitorException;

/**
 * A finisher for the Neos Form framework allowing for subscribing newsletter recipients
 */
class CampaignMonitorSubscriptionFinisher extends AbstractFinisher
{

    /**
     * @Flow\Inject
     * @var CampaignMonitorService
     */
    protected $campaignMonitorService;

    /**
     * @var array
     */
    protected $defaultOptions = [
        'listId'           => '',
        'emailAddress'     => '{email}',
        'name'             => '{name}',
        'additionalFields' => null,
    ];

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     * @return void
     */
    protected function executeInternal()
    {
        $listId = $this->parseOption('listId');
        $emailAddress = $this->parseOption('emailAddress');

        $name = $this->replacePlaceholders($this->parseOption('name'));
        $additionalFields = $this->replacePlaceholders($this->parseOption('additionalFields'));
        try {
            $this->campaignMonitorService->subscribe($listId, $emailAddress, $name, $additionalFields);
        } catch (CampaignMonitorException $exception) {
            throw new FinisherException(sprintf('Failed to subscribe "%s" to list "%s"!', $emailAddress, $listId), 1418060900, $exception);
        }
    }

    /**
     * Recursively replaces "{<var>}" with variables from the form runtime
     *
     * @param  array|mixed $field
     * @return array|mixed
     */
    protected function replacePlaceholders($field)
    {
        if ($field === null) {
            return null;
        }
        if (is_array($field)) {
            return array_map([$this, 'replacePlaceholders'], $field);
        }
        return preg_replace_callback('/{([^}]+)}/', function ($match) {
            return ObjectAccess::getPropertyPath($this->finisherContext->getFormRuntime(), $match[1]);
        }, $field);
    }
}
