<?php

namespace PeterBrain\Core\Block\Adminhtml\System\Config\Module;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use PeterBrain\Core\Helper\ModuleInfoHelper;

/**
 * Class InstalledModules
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 * @package PeterBrain\Core\Block\Adminhtml\System\Config\Module
 */
class InstalledModules extends Field
{
    /**
     * @var ModuleInfoHelper
     */
    private $_moduleInfoHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ModuleInfoHelper $moduleInfoHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleInfoHelper $moduleInfoHelper,
        array $data = []
    ) {
        $this->_moduleInfoHelper = $moduleInfoHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $pbModules = $this->_moduleInfoHelper->getPbModuleList();

        $pbModuleList = array_map(function ($module) {
            return $module['name'] . ' - ' . ($module['version'] ?: '-') . ' : Setup Version: ' . ($module['setup_version'] ?: '-');
        }, $pbModules);

        return implode('<br>', $pbModuleList);
    }
}
