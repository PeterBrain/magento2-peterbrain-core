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
 * Class AbstractData
 * @package PeterBrain\Core\Helper
 */
class AbstractData extends AbstractHelper
{
    const CONFIG_MODULE_PATH = 'pb_core';

    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $backendConfig;

    /**
     * @var array
     */
    protected $isArea = [];

    /**
     * AbstractData constructor.
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
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    /*public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled', $storeId);
    }*/

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
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
    public function getModuleConfig($field = '', $storeId = null)
    {
        $field = ($field !== '') ? '/' . $field : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . $field, $storeId);
    }

    /**
     * @param $field
     * @param null $scopeValue
     * @param string $scopeType
     *
     * @return array|mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if ($scopeValue === null && !$this->isArea()) {
            /** @var Config $backendConfig */
            if (!$this->backendConfig) {
                $this->backendConfig = $this->objectManager->get(\Magento\Backend\App\ConfigInterface::class);
            }

            return $this->backendConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * Is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isArea(Area::AREA_ADMINHTML);
    }

    /**
     * @param string $area
     *
     * @return mixed
     */
    public function isArea($area = Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            /** @var State $state */
            $state = $this->objectManager->get(\Magento\Framework\App\State::class);

            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (Exception $e) {
                $this->isArea[$area] = false;
            }
        }

        return $this->isArea[$area];
    }
}
