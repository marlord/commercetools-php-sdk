<?php
/**
 *
 */

namespace Commercetools\Core\Request\OrderEdits\StagedOrder\Command;

use Commercetools\Core\Request\Orders\Command\OrderSetLineItemCustomFieldAction;

/**
 * @package Commercetools\Core\Request\OrderEdits\StagedOrder\Command
 *
 * @method string getAction()
 * @method StagedOrderSetLineItemCustomFieldAction setAction(string $action = null)
 * @method string getName()
 * @method StagedOrderSetLineItemCustomFieldAction setName(string $name = null)
 * @method string getLineItemId()
 * @method StagedOrderSetLineItemCustomFieldAction setLineItemId(string $lineItemId = null)
 * @method mixed getValue()
 * @method StagedOrderSetLineItemCustomFieldAction setValue($value = null)
 */
// phpcs:ignore
class StagedOrderSetLineItemCustomFieldAction extends OrderSetLineItemCustomFieldAction implements StagedOrderUpdateAction
{
}
