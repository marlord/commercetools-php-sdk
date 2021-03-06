<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\Model\Cart;

use Commercetools\Core\Model\Common\Collection;

/**
 * @package Commercetools\Core\Model\Cart
 * @link https://docs.commercetools.com/http-api-projects-me-carts.html#mylineitemdraft
 * @method MyLineItemDraft current()
 * @method MyLineItemDraftCollection add(MyLineItemDraft $element)
 * @method MyLineItemDraft getAt($offset)
 */
class MyLineItemDraftCollection extends Collection
{
    protected $type = MyLineItemDraft::class;
}
