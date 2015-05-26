<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Core\Request\Products\Command;

use Sphere\Core\Model\Common\Price;
use Sphere\Core\Request\AbstractAction;

/**
 * Class ProductRemovePriceAction
 * @package Sphere\Core\Request\Products\Command
 * @link http://dev.sphere.io/http-api-projects-products.html#remove-price
 * @method string getAction()
 * @method ProductRemovePriceAction setAction(string $action = null)
 * @method Price getPrice()
 * @method ProductRemovePriceAction setPrice(Price $price = null)
 * @method bool getStaged()
 * @method ProductRemovePriceAction setStaged(bool $staged = null)
 * @method int getPriceId()
 * @method ProductRemovePriceAction setPriceId(int $priceId = null)
 */
class ProductRemovePriceAction extends AbstractAction
{
    public function getFields()
    {
        return [
            'action' => [static::TYPE => 'string'],
            'priceId' => [static::TYPE => 'int'],
            'staged' => [static::TYPE => 'bool'],
        ];
    }

    /**
     * @param int $priceId
     * @param Price $price
     */
    public function __construct($priceId)
    {
        $this->setAction('removePrice');
        $this->setPriceId($priceId);
    }
}
