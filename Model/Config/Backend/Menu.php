<?php

namespace PeterBrain\Core\Model\Config\Backend;

use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Value;

/**
 * Class Menu
 * @package PeterBrain\Core\Model\Config\Backend
 */
class Menu extends Value
{
    protected $_resourceName = Data::class;

    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->cleanType(Block::TYPE_IDENTIFIER);
            $this->cacheTypeList->cleanType(Config::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }
}
