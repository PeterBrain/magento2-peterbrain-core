<?php
namespace PeterBrain\Core\Block\Adminhtml\System\Config\Button;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Button;

/**
 * Class UpdateButton
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 * @package PeterBrain\Core\Block\Adminhtml\System\Config\Button
 */
class UpdateButton extends Field
{
    protected $_template = 'PeterBrain_Core::update_button.phtml';/* system/config/ */

    /**
     * Constructor
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return void
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return void
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Create update button
     *
     * @return void
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(Button::class)
            ->setData([
                'id' => 'manual_update_check',
                'label' => __('Check'),
                'class' => 'secondary',
                'onclick' => 'setLocation(\'' . $this->getUrl('pbcore/updater/index') . '\')'
            ]);

        return $button->toHtml();
    }
}
