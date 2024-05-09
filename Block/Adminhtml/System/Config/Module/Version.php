<?php
namespace PeterBrain\Core\Block\Adminhtml\System\Config\Module;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use PeterBrain\Core\Helper\ModuleInfoHelper;

/**
 * Class Version
 * module version information
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class Version extends Field
{
    /**
     * @var ModuleInfoHelper
     */
    private $_moduleInfoHelper;

    /**
     * @var AbstractElement
     */
    private $element;

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
        parent::__construct($context, $data);
        $this->_moduleInfoHelper = $moduleInfoHelper;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
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
        $moduleName = $this->element->getData('field_config', 'module_name');
        $moduleVersion = $this->_moduleInfoHelper->getModuleVersion($moduleName);
        $latestModuleVersion = $this->_moduleInfoHelper->getLatestModuleVersion($moduleName);
        return ($latestModuleVersion && version_compare($latestModuleVersion, $moduleVersion, '>')) ?
            $moduleVersion . ' &rarr; ' . $latestModuleVersion :
            $moduleVersion
        ;
    }
}
