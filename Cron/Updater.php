<?php
namespace PeterBrain\Core\Cron;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\Notification\NotifierInterface;
use PeterBrain\Core\Helper\ModuleInfoHelper;
use PeterBrain\Core\Helper\CoreHelper;

/**
 * Class Updater
 * update check & notifier
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class Updater
{
    /**
     * @var Context
     */
    protected $_context;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Data
     */
    protected $_backendHelper;

    /**
     * @var NotifierInterface
     */
    protected $_notifier;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $_configWriter;

    /**
     * @var CacheInterface
     */
    protected $_cache;

    /**
     * @var CacheTypeList
     */
    protected $_cacheTypeList;

    /**
     * @var ModuleInfoHelper
     */
    protected $_moduleInfoHelper;

    /**
     * @var CoreHelper
     */
    protected $_coreHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Data $backendHelper
     * @param NotifierInterface $notifier
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param CacheInterface $cache
     * @param ModuleInfoHelper $moduleInfoHelper
     * @param CoreHelper $coreHelper
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Data $backendHelper,
        NotifierInterface $notifier,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        CacheInterface $cache,
        TypeListInterface $cacheTypeList,
        ModuleInfoHelper $moduleInfoHelper,
        CoreHelper $coreHelper
    ) {
        $this->_context = $context;
        $this->_logger = $logger;
        $this->_backendHelper = $backendHelper;
        $this->_notifier = $notifier;
        $this->_scopeConfig = $scopeConfig;
        $this->_configWriter = $configWriter;
        $this->_cache = $cache;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_moduleInfoHelper = $moduleInfoHelper;
        $this->_coreHelper = $coreHelper;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $enableAutoCheck = $this->_scopeConfig->getValue(
            'pb_core/updates/auto_update_check',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            false
        );

        if ($enableAutoCheck) {
            $this->checkUpdate();
        }
    }

    /**
     * Check for available updates and store information
     *
     * @return bool
     */
    public function checkUpdate()
    {
        $pbModuleList = $this->_moduleInfoHelper->getPbModuleList();

        foreach ($pbModuleList as $pbModule) {
            $moduleName = $pbModule['name'];
            $moduleVersion = $pbModule['version'];
            $packageName = $this->_moduleInfoHelper->getPackageName($moduleName);
            $configPathModuleName = 'pb' .
                str_replace('peterbrain', '', strtolower($moduleName)) . '/updater/latest_version';
            $cacheKey = 'pb_updater_latest_version_' . strtolower($moduleName);

            $cachedLatestVersion = $this->_cache->load($cacheKey);
            if ($moduleVersion) {
                try {
                    $latestVersion = $this->_moduleInfoHelper->fetchLatestVersion($packageName);

                    if (!isset($cachedLatestVersion) ||
                        version_compare($cachedLatestVersion, $latestVersion, '<')
                    ) { /* check cache for newest version */
                        $this->_cache->save($latestVersion, $cacheKey, [], null);
                        $this->_cacheTypeList->invalidate('config');
                        $storedLatestVersion = $this->_scopeConfig->getValue(
                            $configPathModuleName,
                            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                            false
                        );

                        if ((!isset($storedLatestVersion) ||
                            version_compare($storedLatestVersion, $latestVersion, '<')) &&
                            version_compare($moduleVersion, $latestVersion, '<')
                        ) { /* check system config for newest version */
                            $this->_configWriter->save(
                                $configPathModuleName,
                                $latestVersion,
                                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
                            );
                            !$this->_coreHelper->getConfigGeneral('notifications') ?:
                                $this->notifyUpdate($moduleName, $latestVersion);
                        }
                    }
                } catch (\Exception $e) {
                    $this->_logger->error("Unable to check for updates for module '$moduleName': " . $e->getMessage());
                    continue;
                }
            }
        }

        return true;
    }

    /**
     * Display update notification
     *
     * @param string $moduleName
     * @param string $latestVersion
     *
     * @return void
     */
    private function notifyUpdate(
        string $moduleName,
        string $latestVersion
    ) {
        $url = $this->_moduleInfoHelper->getPackagistUrl($this->_moduleInfoHelper->getPackageName($moduleName));
        $title = __('A new version of %1 is available: %2', str_replace('_', ' ', $moduleName), $latestVersion);
        $description = __('Read more about changes in the new version on Packagist or GitHub.') . ' ' . $url;

        $severity = MessageInterface::SEVERITY_NOTICE;
        $this->_notifier->addNotice(
            $title,
            $description,
            $url,
            $severity
        ); /* addNotice, addMinor, addMajor, addCritical */
    }
}
