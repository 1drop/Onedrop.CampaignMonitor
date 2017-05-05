<?php
namespace Onedrop\CampaignMonitor\Controller\Module;

use Neos\Flow\Annotations as Flow;
use Neos\Error\Messages\Message;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Onedrop\CampaignMonitor\Domain\Service\CampaignMonitorService;
use Onedrop\CampaignMonitor\Exception\CampaignMonitorException;
use Onedrop\CampaignMonitor\Exception\ResourceNotFoundException;

/**
 * Controller for the CampaignMonitor Neos module
 */
class CampaignMonitorController extends AbstractModuleController
{

    /**
     * @Flow\Inject
     * @var CampaignMonitorService
     */
    protected $campaignMonitorService;

    /**
     * @return void
     */
    public function indexAction()
    {
        try {
            $this->view->assign('lists', $this->campaignMonitorService->getLists());
        } catch (CampaignMonitorException $exception) {
            $this->addFlashMessage('An error occurred while trying to fetch lists from CampaignMonitor: "%s"', 'Error', Message::SEVERITY_ERROR, [$exception->getMessage()]);
        }
    }

    /**
     * @param  string $listId
     * @return void
     */
    public function listAction($listId)
    {
        $list = $this->fetchListById($listId);
        $this->view->assign('list', $list);
        try {
            $this->view->assign('members', $this->campaignMonitorService->getMembersByListId($listId));
        } catch (CampaignMonitorException $exception) {
            $this->addFlashMessage('An error occurred while trying to fetch members for list "%s" from CampaignMonitor: "%s"', 'Error', Message::SEVERITY_ERROR, [$list['name'], $exception->getMessage()]);
        }
    }

    /**
     * @param  string $listId
     * @param  string $emailAddress
     * @return void
     */
    public function subscribeAction($listId, $emailAddress)
    {
        $list = $this->fetchListById($listId);
        try {
            $this->campaignMonitorService->subscribe($listId, $emailAddress);
        } catch (CampaignMonitorException $exception) {
            $this->addFlashMessage('An error occurred while trying to subscribe the email "%s" to list "%s": "%s"', 'Error', Message::SEVERITY_ERROR, [$emailAddress, $list->Title, $exception->getMessage()]);
            $this->redirect('list', null, null, ['listId' => $list->ListID]);
        }
        $this->addFlashMessage('Subscribed email "%s" to list "%s". Note: The user will receive an email to confirm the subscription!', 'Success!', Message::SEVERITY_OK, [$emailAddress, $list->Title]);
        $this->redirect('list', null, null, ['listId' => $list->ListID]);
    }

    /**
     * @param  string $listId
     * @param  string $emailAddress
     * @return void
     */
    public function unsubscribeAction($listId, $emailAddress)
    {
        $list = $this->fetchListById($listId);
        try {
            $this->campaignMonitorService->unsubscribe($listId, $emailAddress);
        } catch (CampaignMonitorException $exception) {
            $this->addFlashMessage('An error occurred while trying to unsubscribe the email "%s" from list "%s": "%s"', 'Error', Message::SEVERITY_ERROR, [$emailAddress, $list->Title, $exception->getMessage()]);
            $this->redirect('list', null, null, ['listId' => $list->ListID]);
        }
        $this->addFlashMessage('Unsubscribed email "%s" from list "%s".', 'Success!', Message::SEVERITY_NOTICE, [$emailAddress, $list->Title]);
        $this->redirect('list', null, null, ['listId' => $list->ListID]);
    }

    /**
     * Helper function to fetch a CampaignMonitor list by the given id
     *
     * @param  string $listId
     * @return array
     */
    protected function fetchListById($listId)
    {
        try {
            return $this->campaignMonitorService->getListById($listId);
        } catch (ResourceNotFoundException $exception) {
            $this->addFlashMessage('The list with id "%s" does not exist', 'This list does not exist', Message::SEVERITY_WARNING, [$listId]);
            $this->redirect('index');
        } catch (CampaignMonitorException $exception) {
            $this->addFlashMessage('An error occurred while trying to fetch list with id "%s" from CampaignMonitor: "%s"', 'Error', Message::SEVERITY_ERROR, [$listId, $exception->getMessage()]);
            $this->redirect('index');
        }
        return null;
    }
}
