<?php
namespace PeterBrain\Core\Block\Adminhtml\System\Config\Button;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Button;

/**
 * Class UpdateButton
 * update check button
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class UpdateButton extends Field
{
    protected $_template = 'PeterBrain_Core::update_button.phtml';

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
        $button = $this->getLayout()
            ->createBlock(Button::class)
            ->setData([
                'id' => 'manual_update_check',
                'label' => __('Check'),
                'class' => 'secondary',
                'onclick' => 'setLocation(\'' . $this->getUrl('pbcore/updater/index') . '\')'
            ]);

        return $button->toHtml();
    }
}
