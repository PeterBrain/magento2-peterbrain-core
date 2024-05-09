<?php
namespace PeterBrain\Core\Block\Adminhtml\System\Config\Module;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Developer
 * developer information
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class Developer extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return sprintf(
            '<a href="%s" title="Link to GitHub profile" target="_blank">%s</a>',
            'https://github.com/PeterBrain',
            'PeterBrain'
        );
    }
}
