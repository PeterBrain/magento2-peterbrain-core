<?php

namespace PeterBrain\Core\Block\Adminhtml\System\Config\Module;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use PeterBrain\Core\Helper\ModuleInfo;

class Developer extends Field
{
    /**
     * @var ModuleInfo
     */
    private $_moduleInfoHelper;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleInfo $moduleInfoHelper,
        array $data = []
    ) {
        $this->_moduleInfoHelper = $moduleInfoHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return '<a href="https://github.com/PeterBrain" title="Link to GitHub profile">PeterBrain</a>';
    }
}
