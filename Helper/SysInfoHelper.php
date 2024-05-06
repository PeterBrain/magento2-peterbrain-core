<?php
namespace PeterBrain\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class SysInfoHelper
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 * @package PeterBrain\Core\Helper
 */
class SysInfoHelper extends AbstractHelper
{

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var State
     */
    protected $_appState;

    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var ProductMetadataInterface
     */
    protected $_productMetaData;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DateTime $datetime
     * @param ResourceConnection $resourceConnection
     * @param State $appState
     * @param DirectoryList $directoryList
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        DateTime $datetime,
        ResourceConnection $resourceConnection,
        State $appState,
        DirectoryList $directoryList,
        ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);

        $this->dateTime = $datetime;
        $this->resourceConnection = $resourceConnection;
        $this->_appState = $appState;
        $this->_directoryList = $directoryList;
        $this->_productMetaData = $productMetadata;
    }

    /**
     * Get Magento edition
     *
     * @return string
     */
    public function getMagentoEdition(): string
    {
        return $this->_productMetaData->getEdition();
    }

    /**
     * Get Magento version
     *
     * @return string
     */
    public function getMagentoVersion(): string
    {
        return $this->_productMetaData->getVersion();
    }

    /**
     * Get Magento deployment mode
     *
     * @return string
     */
    public function getMagentoMode(): string
    {
        return $this->_appState->getMode();
    }

    /**
     * Get Magento root directory path
     *
     * @return string
     */
    public function getMagentoPath(): string
    {
        return $this->_directoryList->getRoot();
    }

    /**
     * @return string
     */
    public function getCurrentServerUser()
    {
        return function_exists('get_current_user') ? get_current_user() : __('Unknown');
    }

    /**
     * @return string
     */
    public function getCurrentServerUserGroup()
    {
        $groupid = posix_getegid();
        $groupinfo = posix_getgrgid($groupid);

        return $groupinfo['name'];
    }

    /**
     * @return string
     */
    public function getServerTime()
    {
        return $this->dateTime->gmtDate('Y-m-d H:i:s \U\T\CP');
    }

    /**
     * @return string
     */
    public function getDbTime(): string
    {
        return $this->resourceConnection->getConnection()->fetchOne('select now()');
    }

    /**
     * @return string
     */
    public function getDbVersion(): string
    {
        return $this->resourceConnection->getConnection()->fetchOne('select version()');
    }
}
