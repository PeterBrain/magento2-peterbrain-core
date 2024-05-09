<?php
namespace PeterBrain\Core\Controller\Adminhtml\SysInfo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PeterBrain\Core\Helper\SysInfoHelper;
use PeterBrain\Core\Helper\ModuleInfoHelper;

/**
 * Class Index
 * system info controller
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var SysInfoHelper
     */
    private $_sysInfoHelper;

    /**
     * @var ModuleInfoHelper
     */
    private $_moduleInfoHelper;

    /**
     * @var string
     */
    private $menuId;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SysInfoHelper $sysInfoHelper
     * @param ModuleInfoHelper $moduleInfoHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SysInfoHelper $sysInfoHelper,
        ModuleInfoHelper $moduleInfoHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_sysInfoHelper = $sysInfoHelper;
        $this->_moduleInfoHelper = $moduleInfoHelper;
        $this->menuId = 'PeterBrain_Core::sysinfo';
    }

    /**
     * Load the page defined in view/adminhtml/layout/pbcore_info_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu($this->menuId);
        $resultPage->getConfig()->getTitle()->prepend(__('System Information'));
        $sysInfoHelper = $this->_sysInfoHelper;
        $moduleInfoHelper = $this->_moduleInfoHelper;
        $resultPage->getLayout()->getBlock('system_information')->setData('sysInfoHelper', $sysInfoHelper);
        $resultPage->getLayout()->getBlock('system_information')->setData('moduleInfoHelper', $moduleInfoHelper);

        return $resultPage;
    }
}
