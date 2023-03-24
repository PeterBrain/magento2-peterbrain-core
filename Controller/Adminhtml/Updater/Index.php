<?php
namespace PeterBrain\Core\Controller\Adminhtml\Updater;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PeterBrain\Core\Cron\Updater;

/**
 * Class Index
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 * @package PeterBrain\Core\Controller\Adminhtml\Updater
 */
class Index extends Action
{
    /**
     * @var Updater
     */
    protected $_updater;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Updater $updater
     */
    public function __construct(
        Context $context,
        Updater $updater
    ) {
        parent::__construct($context);
        $this->_updater = $updater;
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $this->_updater->checkUpdate();
            $this->messageManager->addSuccessMessage(__('The update check has been executed successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while executing the update check: %1', $e->getMessage()));
        }
        $this->_redirect('adminhtml/system_config/edit/section/pb_core');
    }
}
