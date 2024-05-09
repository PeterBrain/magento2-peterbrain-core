<?php
namespace PeterBrain\Core\Helper;

use Exception;
use Magento\Backend\App\Config;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CoreHelper
 * main core helper
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class CoreHelper extends AbstractHelper
{
    const CONFIG_MODULE_PATH = 'pb_core';
    const SCOPE_STORE = ScopeInterface::SCOPE_STORE;
    const AREA_FRONTEND = Area::AREA_FRONTEND;
    const AREA_ADMINHTML = Area::AREA_ADMINHTML;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Config
     */
    protected $_backendConfig;

    /**
     * @var array
     */
    protected $isArea = [];

    /**
     * Constructor
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral(string $code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * @param string $field
     * @param null $storeId
     *
     * @return mixed
     */
    public function getModuleConfig(string $field = '', $storeId = null)
    {
        $field = ($field !== '') ? '/' . $field : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . $field, $storeId);
    }

    /**
     * @param string $configPath
     * @param null $scopeValue
     * @param string $scopeType
     *
     * @return string
     */
    public function getConfigValue(string $configPath, $scopeValue = null, $scopeType = self::SCOPE_STORE): string
    {
        if ($scopeValue === null && !$this->isArea()) {
            if (!$this->_backendConfig) {
                $this->_backendConfig = $this->_objectManager->get(Config::class);
            }
            return $this->_backendConfig->getValue($configPath);
        }

        return $this->scopeConfig->getValue(
            $configPath,
            $scopeType,
            $scopeValue
        );
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isArea(static::AREA_ADMINHTML);
    }

    /**
     * @param string $area
     *
     * @return mixed
     */
    public function isArea(string $area = self::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            $state = $this->_objectManager->get(State::class);
            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (Exception $e) {
                $this->isArea[$area] = false;
            }
        }
        return $this->isArea[$area];
    }
}
