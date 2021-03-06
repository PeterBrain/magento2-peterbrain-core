<?php

namespace PeterBrain\Core\Plugin;

use Magento\Backend\Model\Menu\Builder\AbstractCommand;
use PeterBrain\Core\Helper\Data;

/**
 * Class MoveMenu
 * @package PeterBrain\Core\Plugin
 */
class MoveMenu
{
    const MENU_ID = 'PeterBrain_Core::peterbrain';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * MoveMenu constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param AbstractCommand $subject
     * @param $itemParams
     *
     * @return mixed
     */
    public function afterExecute(AbstractCommand $subject, $itemParams)
    {
        if ($this->helper->getConfigGeneral('menu')) {
            if (strpos($itemParams['id'], 'PeterBrain_') !== false && isset($itemParams['parent']) && strpos($itemParams['parent'], 'PeterBrain_') === false) {
                $itemParams['parent'] = self::MENU_ID;
            }
        } elseif ((isset($itemParams['id']) && $itemParams['id'] === self::MENU_ID) || (isset($itemParams['parent']) && $itemParams['parent'] === self::MENU_ID)) {
            $itemParams['removed'] = true;
        }

        return $itemParams;
    }
}
