<?php
namespace PeterBrain\Core\Block\Adminhtml\System\Config\Module;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use PeterBrain\Core\Helper\ModuleInfoHelper;

/**
 * Class InstalledModules
 * installed peterbrain modules
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
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
        parent::__construct($context, $data);
        $this->_moduleInfoHelper = $moduleInfoHelper;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $pbModules = $this->_moduleInfoHelper->getPbModuleList();

        $map_table = function ($module) {
            return '<tr>
                <td>' . $module['name'] . '</td>
                <td>' . ($module['version'] ?: '-') . '</td>
                <td>' . ($module['setup_version'] ?: '-') . '</td>
            </tr>';
        };

        $moduleList = array_map($map_table, $pbModules);

        return '<style>
            .pb_installed-modules td,
            .pb_installed-modules th {
                border: 1px solid #ccc;
                vertical-align: top;
                padding: 5px 15px !important;
                white-space: nowrap;
            }
        </style>
        <table class="pb_installed-modules">
            <thead>
                <tr>
                    <th scope="col">' . /* @noEscape */ __('Module') . '</th>
                    <th scope="col">' . /* @noEscape */ __('Version') . '</th>
                    <th scope="col">' . /* @noEscape */ __('Setup Version') . '</th>
                </tr>
            </thead>
            <tbody>' . $this->escapeHtml(implode('', $moduleList), ['tr', 'td']) . '</tbody>
        </table>';
    }
}
