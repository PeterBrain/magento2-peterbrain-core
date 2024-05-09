<?php
namespace PeterBrain\Core\Helper;

use Exception;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\PackageInfo;

/**
 * Class ModuleInfoHelper
 * module information helper
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class ModuleInfoHelper extends AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var FullModuleList
     */
    protected $_moduleList;

    /**
     * @var PackageInfo
     */
    protected $_packageInfo;

    /**
     * @var ModuleManager
     */
    protected $_moduleManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var array
     */
    protected $_allModuleList = null;

    /**
     * @var array
     */
    protected $_nonMagentoModuleList = null;

    /**
     * @var array
     */
    protected $_magentoModuleList = null;

    /**
     * @var array
     */
    protected $_packageInfoCache = [];

    /**
     * Constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ModuleListInterface $moduleListInterface
     * @param FullModuleList $moduleList
     * @param ModuleManager $moduleManager
     * @param PackageInfo $packageInfo
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        FullModuleList $moduleList,
        ModuleManager $moduleManager,
        PackageInfo $packageInfo,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_moduleList = $moduleList;
        $this->_moduleManager = $moduleManager;
        $this->_packageInfo = $packageInfo;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Get package info by module name
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getPackageInfo(string $moduleName): array
    {
        if (isset($this->_packageInfoCache[$moduleName])) {
            return $this->_packageInfoCache[$moduleName];
        }

        $packageName = $this->_packageInfo->getPackageName($moduleName);

        $packageInfo = [
            'package_name' => $packageName,
            'module_name' => $this->_packageInfo->getModuleName($packageName),
            'version' => $this->_packageInfo->getVersion($moduleName),
            'require' => $this->_packageInfo->getConflict($moduleName),
        ];

        return $packageInfo;
    }

    /**
     * Get package name by module name
     *
     * @param string $moduleName
     *
     * @return string
     */
    public function getPackageName(string $moduleName): string
    {
        $packageName = $this->getPackageInfo($moduleName);
        return $packageName['package_name'];
    }

    /**
     * Get Packagist module URL
     *
     * @param string $package
     *
     * @return string|false
     */
    public function getPackagistUrl(string $package): string
    {
        if ($package != "") {
            $packagistUrl = sprintf('https://packagist.org/packages/%s', $package);

            try {
                $client = new Client();
                $response = $client->get($packagistUrl);

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    return $packagistUrl;
                } else {
                    $this->_logger->error(
                        "URL '$packagistUrl' does not exist or encountered an error. Status code: " .
                        $response->getStatusCode()
                    );
                    return false;
                }
            } catch (RequestException $e) {
                $this->_logger->error("Failed to fetch URL '$packagistUrl': " . $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Fetch the latest version from Packagist API
     *
     * @param string $package
     *
     * @return string|false
     *
     * @throws \Exception
     */
    public function fetchLatestVersion(string $package): ?string
    {
        if ($package != "") {
            $packagistUrl = sprintf('https://repo.packagist.org/p2/%s.json', $package);

            try {
                $client = new Client();
                $response = $client->get($packagistUrl);

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    $data = json_decode($response->getBody(), true);
                    $versions = $data['packages'][$package];
                    $latestVersion = reset($versions)['version'];
                    return $latestVersion;
                } else {
                    $this->_logger->error(
                        "URL '$packagistUrl' does not exist or encountered an error. Status code: " .
                        $response->getStatusCode()
                    );
                    return false;
                }
            } catch (RequestException $e) {
                $this->_logger->error("Failed to fetch latest version for package '$package': " . $e->getMessage());
                /*throw new \Exception(__('Failed to fetch latest version: %1', $e->getMessage()));*/
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get module version by module name
     *
     * @param string $moduleName
     *
     * @return string
     */
    public function getModuleVersion(string $moduleName): string
    {
        $packageInfo = $this->_packageInfo->getVersion($moduleName);
        return $packageInfo;
    }

    /**
     * Get latest module version by module name
     *
     * @param string $moduleName
     *
     * @return string|null
     */
    public function getLatestModuleVersion(string $moduleName): ?string
    {
        $configPathModuleName = 'pb' .
            str_replace('peterbrain', '', strtolower($moduleName)) . '/updater/latest_version';
        $storedLatestVersion = $this->_scopeConfig->getValue(
            $configPathModuleName,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            false
        );
        return $storedLatestVersion;
    }

    /**
     * Get setup version by module name
     *
     * @param string $moduleName
     *
     * @return string|null
     */
    public function getModuleSetupVersion(string $moduleName): ?string
    {
        $moduleInfo = $this->_moduleList->getOne($moduleName);
        return $moduleInfo['setup_version'];
    }

    /**
     * Get module information by module name
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getModuleInfo(string $moduleName): array
    {
        $module = $this->_moduleList->getOne($moduleName);
        $moduleVersion = isset($module['setup_version']) ? $module['setup_version'] : null;

        $moduleInfo = [
            'name' => $moduleName,
            'version' => $this->getModuleVersion($moduleName),
            'setup_version' => $moduleVersion,
            //'sequence' => $module->getSequence(),
            'is_enabled' => $this->_moduleManager->isEnabled($moduleName)
        ];

        return $moduleInfo;
    }

    /**
     * Get a list of all PeterBrain modules with their versions
     *
     * @return array
     */
    public function getPbModuleList(): array
    {
        $moduleList = $this->getNonMagentoModuleList()['enabled_modules'];

        $pbModules = array_map(function ($module) {
            $moduleName = $module['name'];
            if (strpos($moduleName, 'PeterBrain_') !== false) {
                return [
                    'name' => $moduleName,
                    'version' => $this->getModuleVersion($moduleName),
                    'setup_version' => $module['setup_version']
                ];
            }
            return null;
        }, $moduleList);

        return array_filter($pbModules);
    }

    /**
     * Get a list of all modules
     *
     * @return array
     */
    public function getAllModuleList(): array
    {
        if ($this->_allModuleList === null) {
            $moduleList = $this->_moduleList->getAll();

            $createModuleInfo = function ($module) {
                $moduleName = $module['name'];
                return [
                    'name' => $moduleName,
                    'version' => $this->getModuleVersion($moduleName),
                    'setup_version' => $module['setup_version'],
                    'is_enabled' => $this->_moduleManager->isEnabled($moduleName)
                ];
            };

            $this->_allModuleList = [
                'enabled_modules' => [],
                'disabled_modules' => [],
            ];

            $this->_allModuleList['enabled_modules'] = array_map(
                $createModuleInfo,
                array_filter($moduleList, function ($module) {
                    return $this->_moduleManager->isEnabled($module['name']);
                })
            );

            $this->_allModuleList['disabled_modules'] = array_map(
                $createModuleInfo,
                array_filter($moduleList, function ($module) {
                    return !$this->_moduleManager->isEnabled($module['name']);
                })
            );
        }

        return $this->_allModuleList;
    }

    /**
     * Get a list of all Magento modules
     *
     * @return array
     */
    public function getMagentoModuleList(): array
    {
        if ($this->_magentoModuleList === null) {
            $moduleList = $this->getAllModuleList();

            $filterMagentoModules = function ($module) {
                return strpos($module['name'], 'Magento_') === 0;
            };

            $this->_magentoModuleList['enabled_modules'] = array_filter(
                $moduleList['enabled_modules'],
                $filterMagentoModules
            );
            $this->_magentoModuleList['disabled_modules'] = array_filter(
                $moduleList['disabled_modules'],
                $filterMagentoModules
            );
        }

        return $this->_magentoModuleList;
    }

    /**
     * Get a list of all non-Magento modules
     *
     * @return array
     */
    public function getNonMagentoModuleList(): array
    {
        if ($this->_nonMagentoModuleList === null) {
            $moduleList = $this->getAllModuleList();

            $filterNonMagentoModules = function ($module) {
                return strpos($module['name'], 'Magento_') === false;
            };

            $this->_nonMagentoModuleList['enabled_modules'] = array_filter(
                $moduleList['enabled_modules'],
                $filterNonMagentoModules
            );
            $this->_nonMagentoModuleList['disabled_modules'] = array_filter(
                $moduleList['disabled_modules'],
                $filterNonMagentoModules
            );
        }

        return $this->_nonMagentoModuleList;
    }
}
