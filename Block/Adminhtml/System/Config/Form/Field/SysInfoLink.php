<?php
namespace PeterBrain\Core\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class SysInfoLink
 * system information page link
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class SysInfoLink extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return sprintf(
            '<a href="%s">%s</a>',
            $this->_urlBuilder->getUrl('pbcore/sysinfo/index'),
            __('PeterBrain Extensions > Core > System Information')
        );
    }
}
