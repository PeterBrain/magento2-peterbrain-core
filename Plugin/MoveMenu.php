<?php
namespace PeterBrain\Core\Plugin;

use Magento\Backend\Model\Menu\Builder\AbstractCommand;
use PeterBrain\Core\Helper\CoreHelper;

/**
 * Class MoveMenu
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 * @package PeterBrain\Core\Plugin
 */
class MoveMenu
{
    const MENU_ID = 'PeterBrain_Core::peterbrain';

    /**
     * @var CoreHelper
     */
    protected $_coreHelper;

    /**
     * Constructor
     *
     * @param CoreHelper $coreHelper
     */
    public function __construct(
        CoreHelper $coreHelper
    ) {
        $this->_coreHelper = $coreHelper;
    }

    /**
     * @param AbstractCommand $subject
     * @param array $itemParams
     *
     * @return mixed
     */
    public function afterExecute(AbstractCommand $subject, $itemParams)
    {
        if ($this->_coreHelper->getConfigGeneral('menu')) {
            if (strpos($itemParams['id'], 'PeterBrain_') !== false && isset($itemParams['parent']) && strpos($itemParams['parent'], 'PeterBrain_') === false) {
                $itemParams['parent'] = self::MENU_ID;
            }
        } elseif ((isset($itemParams['id']) && $itemParams['id'] === self::MENU_ID) || (isset($itemParams['parent']) && $itemParams['parent'] === self::MENU_ID)) {
            $itemParams['removed'] = true;
        }

        return $itemParams;
    }
}
