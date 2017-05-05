<?php
namespace Onedrop\CampaignMonitor\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryResultInterface;
use Onedrop\CampaignMonitor\Domain\Dto\CallbackQuery;
use Onedrop\CampaignMonitor\Domain\Dto\CallbackQueryResult;
use Onedrop\CampaignMonitor\Exception\InvalidApiKeyException;
use Onedrop\CampaignMonitor\Exception\ResourceNotFoundException;

/**
 * Central authority to be used when interacting with the CampaignMonitor API
 *
 * @Flow\Scope("singleton")
 */
class CampaignMonitorService
{

    /**
     * @var array
     */
    protected $auth;

    /**
     * @var string
     */
    protected $clientID;

    /**
     * @param  string                 $apiKey
     * @param  string                 $clientID
     * @throws InvalidApiKeyException
     */
    public function __construct($apiKey, $clientID)
    {
        $this->auth = ['api_key' => $apiKey];
        $this->clientID = $clientID;
        $wrap = new \CS_REST_General($this->auth);
        $result = $wrap->get_clients();

        if (!$result->was_successful()) {
            throw new InvalidApiKeyException(sprintf('Invalid CampaignMonitor API key %s supplied.', $apiKey), 1483531773);
        }
    }

    /**
     * @return CallbackQueryResult|QueryResultInterface
     */
    public function getLists()
    {
        $query = new CallbackQuery(function () {
            $wrap = new \CS_REST_Clients($this->clientID, $this->auth);
            $result = $wrap->get_lists();

            /** @var \stdClass $item */
            foreach ($result->response as $item) {
                $list = $this->getListById($item->ListID);
                $item->MemberCount = $list->MemberCount;
            }

            return $result->response;
        });
        return $query->execute();
    }

    /**
     * @param  string $listId
     * @return array
     */
    public function getListById($listId)
    {
        $wrap = new \CS_REST_Lists($listId, $this->auth);
        $result = $wrap->get()->response;
        $result->MemberCount = count($this->getMembersByListId($listId));

        return $result;
    }

    /**
     * @param  string                                   $listId
     * @return CallbackQueryResult|QueryResultInterface
     */
    public function getMembersByListId($listId)
    {
        $memberQuery = new CallbackQuery(function (CallbackQuery $query) use ($listId) {
            $wrap = new \CS_REST_Lists($listId, $this->auth);
            $members = $wrap->get_active_subscribers()->response->Results;

            for ($i = 0; $i < count($members); ++$i) {
                $members[$i]->ID = $i;
            }

            return $members;
        });
        return $memberQuery->execute();
    }

    /**
     * @param  string $listId
     * @param  string $emailAddress
     * @return bool
     */
    public function isMember($listId, $emailAddress)
    {
        try {
            $wrap = new \CS_REST_Subscribers($listId, $this->auth);
            $wrap->get($emailAddress);
            return true;
        } catch (ResourceNotFoundException $exception) {
            return false;
        }
    }

    /**
     * @param  string $listId
     * @param  string $emailAddress
     * @return array
     */
    public function getMemberInfo($listId, $emailAddress)
    {
        $wrap = new \CS_REST_Subscribers($listId, $this->auth);
        $member = $wrap->get($emailAddress);
        return $member->response;
    }

    /**
     * @param  string $listId
     * @param  string $emailAddress
     * @param  array  $additionalFields
     * @param  mixed  $subscriberName
     * @return void
     */
    public function subscribe($listId, $emailAddress, $subscriberName = '', array $additionalFields = null)
    {
        $wrap = new \CS_REST_Subscribers($listId, $this->auth);
        $wrap->add([
            'EmailAddress' => $emailAddress,
            'Name'         => $subscriberName,
            'CustomFields' => $additionalFields,
            'Resubscribe'  => true,
        ]);
    }

    /**
     * @param  string $listId
     * @param  string $emailAddress
     * @return void
     */
    public function unsubscribe($listId, $emailAddress)
    {
        $wrap = new \CS_REST_Subscribers($listId, $this->auth);
        $wrap->unsubscribe($emailAddress);
    }
}
