<?php
namespace PeterBrain\Core\Model\Config\Backend;

use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Value;

/**
 * Class Menu
 * menu cache invalidation
 *
 * @author PeterBrain <peter.loecker@live.at>
 * @copyright Copyright (c) PeterBrain (https://peterbrain.com/)
 */
class Menu extends Value
{
    /**
     * @var Data
     */
    protected $_resourceName = Data::class;

    /**
     * @return void
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->cleanType(Block::TYPE_IDENTIFIER);
            $this->cacheTypeList->cleanType(Config::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }
}
