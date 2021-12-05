<?php

namespace PeterBrain\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class Data
 * @package PeterBrain\Core\Helper
 */
class ModuleInfo extends AbstractHelper
{
    const MODULE_NAME = 'PeterBrain_Core';
    const CONFIG_MODULE_PATH = 'peterbrain_core';

    protected $_appState;
    protected $_directoryList;

    /**
     * AbstractData constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        State $appState,
        DirectoryList $directoryList
    ) {
        $this->_productMetaData = $productMetadata;
        $this->_moduleList = $moduleList;
        $this->_appState = $appState;
        $this->_directoryList = $directoryList;

        parent::__construct($context);
    }

    /**
     * get module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        /*return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];*/
        $moduleCode = 'PeterBrain_Core';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }

    /**
     * get Magento edition
     *
     * @return string
     */
    public function getMagentoEdition()
    {
        return $this->_productMetaData->getEdition();
    }

    /**
     * get Magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->_productMetaData->getVersion();
    }

    /**
     * get Magento deployment mode
     *
     * @return string
     */
    public function getMagentoMode()
    {
        return $this->_appState->getMode();
    }

    /**
     * get Magento installation path
     *
     * @return string
     */
    public function getMagentoPath()
    {
        return $this->_directoryList->getRoot();
    }
}
