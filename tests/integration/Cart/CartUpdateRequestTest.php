<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\IntegrationTests\Cart;

use Commercetools\Core\Builder\Request\RequestBuilder;
use Commercetools\Core\Fixtures\FixtureException;
use Commercetools\Core\IntegrationTests\ApiTestCase;
use Commercetools\Core\IntegrationTests\CartDiscount\CartDiscountFixture;
use Commercetools\Core\IntegrationTests\Customer\CustomerFixture;
use Commercetools\Core\IntegrationTests\CustomerGroup\CustomerGroupFixture;
use Commercetools\Core\IntegrationTests\DiscountCode\DiscountCodeFixture;
use Commercetools\Core\IntegrationTests\Payment\PaymentFixture;
use Commercetools\Core\IntegrationTests\Product\ProductFixture;
use Commercetools\Core\IntegrationTests\Project\ProjectFixture;
use Commercetools\Core\IntegrationTests\ShippingMethod\ShippingMethodFixture;
use Commercetools\Core\IntegrationTests\Store\StoreFixture;
use Commercetools\Core\IntegrationTests\TaxCategory\TaxCategoryFixture;
use Commercetools\Core\IntegrationTests\Type\TypeFixture;
use Commercetools\Core\IntegrationTests\Zone\ZoneFixture;
use Commercetools\Core\Model\Cart\Cart;
use Commercetools\Core\Model\Cart\CartDraft;
use Commercetools\Core\Model\Cart\CartState;
use Commercetools\Core\Model\Cart\CustomLineItemDraft;
use Commercetools\Core\Model\Cart\CustomLineItemDraftCollection;
use Commercetools\Core\Model\Cart\ExternalLineItemTotalPrice;
use Commercetools\Core\Model\Cart\ExternalTaxAmountDraft;
use Commercetools\Core\Model\Cart\ItemShippingDetailsDraft;
use Commercetools\Core\Model\Cart\ItemShippingTarget;
use Commercetools\Core\Model\Cart\ItemShippingTargetCollection;
use Commercetools\Core\Model\Cart\LineItem;
use Commercetools\Core\Model\Cart\LineItemDraft;
use Commercetools\Core\Model\Cart\LineItemDraftCollection;
use Commercetools\Core\Model\Cart\ScoreShippingRateInput;
use Commercetools\Core\Model\CartDiscount\AbsoluteCartDiscountValue;
use Commercetools\Core\Model\CartDiscount\CartDiscount;
use Commercetools\Core\Model\CartDiscount\CartDiscountDraft;
use Commercetools\Core\Model\CartDiscount\CartDiscountReference;
use Commercetools\Core\Model\CartDiscount\CartDiscountTarget;
use Commercetools\Core\Model\CartDiscount\CartDiscountValue;
use Commercetools\Core\Model\CartDiscount\LineItemsTarget;
use Commercetools\Core\Model\CartDiscount\MultiBuyCustomLineItemsTarget;
use Commercetools\Core\Model\CartDiscount\MultiBuyLineItemsTarget;
use Commercetools\Core\Model\CartDiscount\RelativeCartDiscountValue;
use Commercetools\Core\Model\Common\Address;
use Commercetools\Core\Model\Common\AddressCollection;
use Commercetools\Core\Model\Common\LocalizedString;
use Commercetools\Core\Model\Common\Money;
use Commercetools\Core\Model\Common\MoneyCollection;
use Commercetools\Core\Model\Common\PriceDraft;
use Commercetools\Core\Model\Common\PriceTier;
use Commercetools\Core\Model\Common\PriceTierCollection;
use Commercetools\Core\Model\Customer\Customer;
use Commercetools\Core\Model\CustomerGroup\CustomerGroup;
use Commercetools\Core\Model\CustomField\CustomFieldObject;
use Commercetools\Core\Model\CustomField\CustomFieldObjectDraft;
use Commercetools\Core\Model\CustomField\FieldContainer;
use Commercetools\Core\Model\DiscountCode\DiscountCode;
use Commercetools\Core\Model\DiscountCode\DiscountCodeDraft;
use Commercetools\Core\Model\Payment\Payment;
use Commercetools\Core\Model\Product\Product;
use Commercetools\Core\Model\Product\ProductDraft;
use Commercetools\Core\Model\Project\CartScoreType;
use Commercetools\Core\Model\Project\Project;
use Commercetools\Core\Model\ShippingMethod\ShippingMethod;
use Commercetools\Core\Model\ShippingMethod\ShippingRate;
use Commercetools\Core\Model\Store\Store;
use Commercetools\Core\Model\Store\StoreReference;
use Commercetools\Core\Model\TaxCategory\ExternalTaxRateDraft;
use Commercetools\Core\Model\TaxCategory\TaxCategory;
use Commercetools\Core\Model\TaxCategory\TaxRate;
use Commercetools\Core\Model\Type\Type;
use Commercetools\Core\Model\Type\TypeDraft;
use Commercetools\Core\Model\Zone\Zone;
use Commercetools\Core\Request\CartDiscounts\CartDiscountCreateRequest;
use Commercetools\Core\Request\Carts\CartByIdGetRequest;
use Commercetools\Core\Request\Carts\CartCreateRequest;
use Commercetools\Core\Request\Carts\CartDeleteRequest;
use Commercetools\Core\Request\Carts\CartQueryRequest;
use Commercetools\Core\Request\Carts\CartUpdateRequest;
use Commercetools\Core\Request\Carts\Command\CartAddCustomLineItemAction;
use Commercetools\Core\Request\Carts\Command\CartAddDiscountCodeAction;
use Commercetools\Core\Request\Carts\Command\CartAddItemShippingAddressAction;
use Commercetools\Core\Request\Carts\Command\CartAddLineItemAction;
use Commercetools\Core\Request\Carts\Command\CartAddPaymentAction;
use Commercetools\Core\Request\Carts\Command\CartApplyDeltaToCustomLineItemShippingDetailsTargetsAction;
use Commercetools\Core\Request\Carts\Command\CartApplyDeltaToLineItemShippingDetailsTargetsAction;
use Commercetools\Core\Request\Carts\Command\CartChangeCustomLineItemMoneyAction;
use Commercetools\Core\Request\Carts\Command\CartChangeCustomLineItemQuantityAction;
use Commercetools\Core\Request\Carts\Command\CartChangeLineItemQuantityAction;
use Commercetools\Core\Request\Carts\Command\CartChangeTaxCalculationModeAction;
use Commercetools\Core\Request\Carts\Command\CartChangeTaxRoundingModeAction;
use Commercetools\Core\Request\Carts\Command\CartRecalculateAction;
use Commercetools\Core\Request\Carts\Command\CartRemoveCustomLineItemAction;
use Commercetools\Core\Request\Carts\Command\CartRemoveDiscountCodeAction;
use Commercetools\Core\Request\Carts\Command\CartRemoveItemShippingAddressAction;
use Commercetools\Core\Request\Carts\Command\CartRemoveLineItemAction;
use Commercetools\Core\Request\Carts\Command\CartRemovePaymentAction;
use Commercetools\Core\Request\Carts\Command\CartSetAnonymousIdAction;
use Commercetools\Core\Request\Carts\Command\CartSetBillingAddressAction;
use Commercetools\Core\Request\Carts\Command\CartSetCartTotalTaxAction;
use Commercetools\Core\Request\Carts\Command\CartSetCountryAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomerEmailAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomerGroupAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomerIdAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomLineItemCustomFieldAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomLineItemCustomTypeAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomLineItemShippingDetailsAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomLineItemTaxAmountAction;
use Commercetools\Core\Request\Carts\Command\CartSetCustomShippingMethodAction;
use Commercetools\Core\Request\Carts\Command\CartSetDeleteDaysAfterLastModificationAction;
use Commercetools\Core\Request\Carts\Command\CartSetLineItemCustomFieldAction;
use Commercetools\Core\Request\Carts\Command\CartSetLineItemCustomTypeAction;
use Commercetools\Core\Request\Carts\Command\CartSetLineItemPriceAction;
use Commercetools\Core\Request\Carts\Command\CartSetLineItemShippingDetailsAction;
use Commercetools\Core\Request\Carts\Command\CartSetLineItemTaxAmountAction;
use Commercetools\Core\Request\Carts\Command\CartSetLineItemTotalPriceAction;
use Commercetools\Core\Request\Carts\Command\CartSetLocaleAction;
use Commercetools\Core\Request\Carts\Command\CartSetShippingAddressAction;
use Commercetools\Core\Request\Carts\Command\CartSetShippingMethodAction;
use Commercetools\Core\Request\Carts\Command\CartSetShippingMethodTaxAmountAction;
use Commercetools\Core\Request\Carts\Command\CartSetShippingRateInputAction;
use Commercetools\Core\Request\Carts\Command\CartUpdateItemShippingAddressAction;
use Commercetools\Core\Request\Customers\CustomerLoginRequest;
use Commercetools\Core\Request\CustomField\Command\SetCustomFieldAction;
use Commercetools\Core\Request\CustomField\Command\SetCustomTypeAction;
use Commercetools\Core\Request\DiscountCodes\Command\DiscountCodeChangeCartDiscountsAction;
use Commercetools\Core\Request\InStores\InStoreRequestDecorator;
use Commercetools\Core\Request\Products\Command\ProductChangeNameAction;
use Commercetools\Core\Request\Products\Command\ProductChangePriceAction;
use Commercetools\Core\Request\Products\Command\ProductPublishAction;
use Commercetools\Core\Request\Products\ProductUpdateRequest;
use Commercetools\Core\Request\Project\Command\ProjectSetShippingRateInputTypeAction;
use Commercetools\Core\Request\Project\ProjectGetRequest;
use Commercetools\Core\Request\Project\ProjectUpdateRequest;
use Commercetools\Core\IntegrationTests\TestHelper;
use Commercetools\Core\Request\TaxCategories\Command\TaxCategoryAddTaxRateAction;

class CartUpdateRequestTest extends ApiTestCase
{
    public function testLineItemsOfRemovedProducts()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product) {
                        $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $request = RequestBuilder::of()->carts()->getById($cart->getId());
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertNotEmpty($cart->getLineItems());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRecalculateAction::of());
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        if ($response->getStatusCode() !== 200) {
                            $this->expectException(FixtureException::class);
                        }

                        $request = RequestBuilder::of()->carts()->getById($cart->getId());
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $lineItem = $cart->getLineItems()->current()->getId();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRemoveLineItemAction::ofLineItemId($lineItem));
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertEmpty($cart->getLineItems());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRecalculateAction::of());
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        return $result;
                    }
                );
            }
        );
    }

    public function testLineItem()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product) {
                        $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            $product->getProductType()->getId(),
                            $cart->getLineItems()->current()->getProductType()->getId()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartChangeLineItemQuantityAction::ofLineItemIdAndQuantity(
                                    $cart->getLineItems()->current()->getId(),
                                    2
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(2, $cart->getLineItems()->current()->getQuantity());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartRemoveLineItemAction::ofLineItemId($cart->getLineItems()->current()->getId())
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertCount(0, $result->getLineItems());

                        return $result;
                    }
                );
            }
        );
    }

    public function testLineItemBySku()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $cartDraft) use ($variant) {
                        return $cartDraft->setLineItems(
                            LineItemDraftCollection::of()->add(LineItemDraft::ofSku($variant->getSku()))
                        );
                    },
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofSkuAndQuantity($variant->getSku(), 1)
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(2, $result->getLineItems()->current()->getQuantity());

                        return $result;
                    }
                );
            }
        );
    }

    public function testSetExternalLineItemTotalPrice()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            LineItem::PRICE_MODE_PLATFORM,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            100,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(100, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemTotalPriceAction::ofLineItemId($cart->getLineItems()->current()->getId())
                                    ->setExternalTotalPrice(
                                        ExternalLineItemTotalPrice::of()
                                            ->setPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                                            ->setTotalPrice(Money::ofCurrencyAndAmount('EUR', 12345678))
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_TOTAL,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(12345678, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemTotalPriceAction::ofLineItemId($cart->getLineItems()->current()->getId())
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_PLATFORM,
                            $result->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            100,
                            $result->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(100, $result->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testSetExternalLineItemPrice()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    2
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            LineItem::PRICE_MODE_PLATFORM,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            100,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(200, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemPriceAction::ofLineItemId($cart->getLineItems()->current()->getId())
                                    ->setExternalPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_PRICE,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(24690, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemPriceAction::ofLineItemId($cart->getLineItems()->current()->getId())
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_PLATFORM,
                            $result->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            100,
                            $result->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(200, $result->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testUnsetExternalLineItemTotalPriceQuantityChange()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            LineItem::PRICE_MODE_PLATFORM,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            100,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(100, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemTotalPriceAction::ofLineItemId($cart->getLineItems()->current()->getId())
                                    ->setExternalTotalPrice(
                                        ExternalLineItemTotalPrice::of()
                                            ->setPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                                            ->setTotalPrice(Money::ofCurrencyAndAmount('EUR', 12345678))
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_TOTAL,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(12345678, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartChangeLineItemQuantityAction::ofLineItemIdAndQuantity(
                                    $cart->getLineItems()->current()->getId(),
                                    2
                                )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_PLATFORM,
                            $result->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            100,
                            $result->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(200, $result->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testExternalLineItemTotalPriceQuantityChange()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    2
                                )
                                    ->setExternalTotalPrice(
                                        ExternalLineItemTotalPrice::of()
                                            ->setPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                                            ->setTotalPrice(Money::ofCurrencyAndAmount('EUR', 12345678))
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_TOTAL,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(12345678, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartChangeLineItemQuantityAction::ofLineItemIdAndQuantity(
                                    $cart->getLineItems()->current()->getId(),
                                    3
                                )
                                    ->setExternalTotalPrice(
                                        ExternalLineItemTotalPrice::of()
                                            ->setPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                                            ->setTotalPrice(Money::ofCurrencyAndAmount('EUR', 12345679))
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_TOTAL,
                            $result->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $result->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(
                            12345679,
                            $result->getLineItems()->current()->getTotalPrice()->getCentAmount()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testExternalLineItemPriceQuantityChange()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    2
                                )
                                    ->setExternalPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_PRICE,
                            $cart->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(24690, $cart->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartChangeLineItemQuantityAction::ofLineItemIdAndQuantity(
                                    $cart->getLineItems()->current()->getId(),
                                    3
                                )
                                    ->setExternalPrice(Money::ofCurrencyAndAmount('EUR', 12345))
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            LineItem::PRICE_MODE_EXTERNAL_PRICE,
                            $result->getLineItems()->current()->getPriceMode()
                        );
                        $this->assertSame(
                            12345,
                            $result->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );
                        $this->assertSame(37035, $result->getLineItems()->current()->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testCustomLineItem()
    {
        $client = $this->getApiClient();

        TaxCategoryFixture::withTaxCategory(
            $client,
            function (TaxCategory $taxCategory) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $taxCategory) {
                        $name = LocalizedString::ofLangAndText('en', 'test-' . CartFixture::uniqueCartString());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddCustomLineItemAction::ofNameQuantityMoneySlugAndTaxCategory(
                                    $name,
                                    1,
                                    Money::ofCurrencyAndAmount('EUR', 100),
                                    $name->en,
                                    $taxCategory->getReference()
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($name->en, $cart->getCustomLineItems()->current()->getName()->en);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartRemoveCustomLineItemAction::ofCustomLineItemId(
                                    $cart->getCustomLineItems()->current()->getId()
                                )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertCount(0, $cart->getLineItems());

                        return $result;
                    }
                );
            }
        );
    }

    public function testCustomLineItemChangeQuantity()
    {
        $client = $this->getApiClient();
        $name = LocalizedString::ofLangAndText('en', 'test-' . CartFixture::uniqueCartString());

        TaxCategoryFixture::withTaxCategory(
            $client,
            function (TaxCategory $taxCategory) use ($client, $name) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($name, $taxCategory) {
                        return $draft->setCustomLineItems(
                            CustomLineItemDraftCollection::of()
                                ->add(
                                    CustomLineItemDraft::ofNameMoneySlugTaxCategoryAndQuantity(
                                        $name,
                                        Money::ofCurrencyAndAmount('EUR', 100),
                                        $name->en,
                                        $taxCategory->getReference(),
                                        1
                                    )
                                )
                        );
                    },
                    function (Cart $cart) use ($client, $taxCategory) {
                        $this->assertSame(
                            100,
                            $cart->getCustomLineItems()->current()->getTotalPrice()->getCentAmount()
                        );
                        $this->assertSame(100, $cart->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartChangeCustomLineItemQuantityAction::ofCustomLineItemIdAndQuantity(
                                    $cart->getCustomLineItems()->current()->getId(),
                                    2
                                )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(2, $result->getCustomLineItems()->current()->getQuantity());
                        $this->assertSame(
                            200,
                            $result->getCustomLineItems()->current()->getTotalPrice()->getCentAmount()
                        );
                        $this->assertSame(200, $result->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testCustomLineItemChangeMoney()
    {
        $client = $this->getApiClient();
        $name = LocalizedString::ofLangAndText('en', 'test-' . CartFixture::uniqueCartString());

        TaxCategoryFixture::withTaxCategory(
            $client,
            function (TaxCategory $taxCategory) use ($client, $name) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($name, $taxCategory) {
                        return $draft->setCustomLineItems(
                            CustomLineItemDraftCollection::of()
                                ->add(
                                    CustomLineItemDraft::ofNameMoneySlugTaxCategoryAndQuantity(
                                        $name,
                                        Money::ofCurrencyAndAmount('EUR', 100),
                                        $name->en,
                                        $taxCategory->getReference(),
                                        2
                                    )
                                )
                        );
                    },
                    function (Cart $cart) use ($client, $taxCategory) {
                        $this->assertSame(100, $cart->getCustomLineItems()->current()->getMoney()->getCentAmount());
                        $this->assertSame(
                            200,
                            $cart->getCustomLineItems()->current()->getTotalPrice()->getCentAmount()
                        );
                        $this->assertSame(200, $cart->getTotalPrice()->getCentAmount());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartChangeCustomLineItemMoneyAction::ofCustomLineItemIdAndMoney(
                                    $cart->getCustomLineItems()->current()->getId(),
                                    Money::ofCurrencyAndAmount('EUR', 200)
                                )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(2, $result->getCustomLineItems()->current()->getQuantity());
                        $this->assertSame(200, $result->getCustomLineItems()->current()->getMoney()->getCentAmount());
                        $this->assertSame(
                            400,
                            $result->getCustomLineItems()->current()->getTotalPrice()->getCentAmount()
                        );
                        $this->assertSame(400, $result->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }
// todo
    public function testCustomLineItemMerge()
    {
        $name = LocalizedString::ofLangAndText('en', 'test-' . $this->getTestRun());
        $anonName = LocalizedString::ofLangAndText('en', 'anon-' . $this->getTestRun());
        $customerDraft = $this->getCustomerDraft();
        $customer = $this->getCustomer($customerDraft);

        $draft = $this->getDraft();
        $draft->setCustomerId($customer->getId());
        $customerCart = $this->createCart($draft);

        $this->assertCount(0, $customerCart->getCustomLineItems());

        $anonCartDraft = $this->getDraft();
        $anonCartDraft->setCustomLineItems(
            CustomLineItemDraftCollection::of()
                ->add(
                    CustomLineItemDraft::ofNameMoneySlugTaxCategoryAndQuantity(
                        $anonName,
                        Money::ofCurrencyAndAmount('EUR', 100),
                        $anonName->en,
                        $this->getTaxCategory()->getReference(),
                        1
                    )
                )
        );
        $request = CartCreateRequest::ofDraft($anonCartDraft);
        $response = $request->executeWithClient($this->getClient());
        $anonCart = $request->mapResponse($response);
        $this->assertSame(CartState::ACTIVE, $anonCart->getCartState());

        $this->assertNotSame($customerCart->getId(), $anonCart->getId());

        $loginRequest = CustomerLoginRequest::ofEmailAndPassword(
            $customerDraft->getEmail(),
            $customerDraft->getPassword(),
            $anonCart->getId()
        );
        $response = $loginRequest->executeWithClient($this->getClient());
        $result = $loginRequest->mapResponse($response);
        $loginCart = $result->getCart();
        $this->assertSame(CartState::ACTIVE, $loginCart->getCartState());

        if ($loginCart->getCustomLineItems()->count() == 0) {
            $this->markTestSkipped(
                'Merging custom line items from anon carts to customer cart not yet supported by API.'
            );
        }
        $this->assertCount(1, $loginCart->getCustomLineItems());
        $this->assertSame($anonName->en, $loginCart->getCustomLineItems()->current()->getSlug());

        $anonCartRequest = CartByIdGetRequest::ofId($anonCart->getId());
        $response = $anonCartRequest->executeWithClient($this->getClient());
        $anonCart = $request->mapResponse($response);

        $customerCartRequest = CartByIdGetRequest::ofId($anonCart->getId());
        $response = $customerCartRequest->executeWithClient($this->getClient());
        $customerCart = $request->mapResponse($response);

        $this->deleteRequest->setVersion($customerCart->getVersion());
        $this->cleanupRequests[] = CartDeleteRequest::ofIdAndVersion($anonCart->getId(), $anonCart->getVersion());

        $this->assertSame(CartState::MERGED, $anonCart->getCartState());
    }

    public function testCustomerEmail()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client) {
                $email = 'test-' . CartFixture::uniqueCartString() . '@example.com';

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetCustomerEmailAction::of()->setEmail($email));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame($email, $result->getCustomerEmail());

                return $result;
            }
        );
    }

    public function testShippingAddress()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client) {
                $address = Address::of()
                    ->setFirstName('test-' . CartFixture::uniqueCartString() . '@example.com')
                    ->setCountry('DE');
                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetShippingAddressAction::of()->setAddress($address));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame($address->getFirstName(), $result->getShippingAddress()->getFirstName());

                return $result;
            }
        );
    }

    public function testBillingAddress()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client) {
                $address = Address::of()
                    ->setFirstName('test-' . CartFixture::uniqueCartString() . '@example.com')
                    ->setCountry('DE');
                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetBillingAddressAction::of()->setAddress($address));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame($address->getFirstName(), $result->getBillingAddress()->getFirstName());

                return $result;
            }
        );
    }

    public function testSetCountry()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client) {
                $country = 'UK';

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetCountryAction::of()->setCountry($country));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame($country, $result->getCountry());

                return $result;
            }
        );
    }

    public function testSetAnonymousId()
    {
        $client = $this->getApiClient();
        $anonymousId = uniqid();
        $newAnonymousId = uniqid();

        CartFixture::withUpdateableDraftCart(
            $client,
            function (CartDraft $draft) use ($anonymousId) {
                return $draft->setAnonymousId($anonymousId);
            },
            function (Cart $cart) use ($client, $newAnonymousId) {
                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetAnonymousIdAction::of()->setAnonymousId($newAnonymousId));
                $response = $this->execute($client, $request);
                $cart = $request->mapFromResponse($response);

                $this->assertSame($newAnonymousId, $cart->getAnonymousId());

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetAnonymousIdAction::of());
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertNull($result->getAnonymousId());

                return $result;
            }
        );
    }

    public function testSetShippingMethod()
    {
        $client = $this->getApiClient();

        ShippingMethodFixture::withShippingMethod(
            $client,
            function (ShippingMethod $shippingMethod, Zone $zone, TaxCategory $taxCategory) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $shippingMethod, $zone, $taxCategory) {
                        $region = $zone->getLocations()->current()->getState();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetShippingAddressAction::of()->setAddress(
                                    Address::of()->setCountry('DE')
                                        ->setState($region)
                                )
                            )->addAction(
                                CartSetShippingMethodAction::of()->setShippingMethod($shippingMethod->getReference())
                            );

                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            $shippingMethod->getName(),
                            $result->getShippingInfo()->getShippingMethodName()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testSetCustomShippingMethod()
    {
        $client = $this->getApiClient();

        ShippingMethodFixture::withShippingMethod(
            $client,
            function (ShippingMethod $shippingMethod, Zone $zone, TaxCategory $taxCategory) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $shippingMethod, $zone, $taxCategory) {
                        $region = $zone->getLocations()->current()->getState();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetShippingAddressAction::of()->setAddress(
                                    Address::of()->setCountry('DE')->setState($region)
                                )
                            )->addAction(
                                CartSetCustomShippingMethodAction::of()
                                    ->setShippingMethodName($shippingMethod->getName())
                                    ->setShippingRate(
                                        ShippingRate::of()
                                            ->setPrice(Money::ofCurrencyAndAmount('EUR', 100))
                                    )->setTaxCategory($taxCategory->getReference())
                            );

                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            $shippingMethod->getName(),
                            $result->getShippingInfo()->getShippingMethodName()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testCustomerId()
    {
        $client = $this->getApiClient();

        CustomerFixture::withCustomer(
            $client,
            function (Customer $customer) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $customer) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartSetCustomerIdAction::of()->setCustomerId($customer->getId()));
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame($customer->getId(), $result->getCustomerId());

                        return $result;
                    }
                );
            }
        );
    }

    public function testRecalculate()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product) {
                        $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $request = RequestBuilder::of()->products()->update($product)
                            ->addAction(
                                ProductChangePriceAction::ofPriceIdAndPrice(
                                    $variant->getPrices()->current()->getId(),
                                    PriceDraft::ofMoney(Money::ofCurrencyAndAmount('EUR', 200))
                                )
                            )
                            ->addAction(ProductPublishAction::of());

                        $client->execute($request);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRecalculateAction::of());
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(200, $result->getTotalPrice()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testRecalculateChangedProduct()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $product) {
                        $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $request = RequestBuilder::of()->carts()->query()
                            ->where('id=:id', ['id' => $cart->getId()]);
                        $response = $this->execute($client, $request);
                        $cart2 = $request->mapFromResponse($response);

                        $this->assertNotEmpty((string)$cart2->current()->getLineItems()->current()->getProductSlug());

                        $newName = 'new-name-' . CartFixture::uniqueCartString();

                        $request = RequestBuilder::of()->products()->update($product)
                            ->addAction(
                                ProductChangeNameAction::ofName(
                                    LocalizedString::ofLangAndText('en', $newName)
                                )
                            )
                            ->addAction(ProductPublishAction::of());
                        $client->execute($request);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRecalculateAction::of());
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(100, $cart->getTotalPrice()->getCentAmount());
                        $this->assertSame(
                            (string)$product->getMasterData()->getCurrent()->getName(),
                            (string)$cart->getLineItems()->current()->getName()
                        );
                        $this->assertNotEmpty((string)$cart->getLineItems()->current()->getProductSlug());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRecalculateAction::of()->setUpdateProductData(true));
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(100, $result->getTotalPrice()->getCentAmount());
                        $this->assertSame($newName, (string)$result->getLineItems()->current()->getName());
                        $this->assertNotEmpty((string)$result->getLineItems()->current()->getProductSlug());

                        return $result;
                    }
                );
            }
        );
    }

    public function testCustomType()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())->setResourceTypeIds(['order']);
            },
            function (Type $type) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $type) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                SetCustomTypeAction::ofTypeKey($type->getKey())
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame($type->getId(), $result->getCustom()->getType()->getId());

                        return $result;
                    }
                );
            }
        );
    }

    public function testCustomField()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())->setResourceTypeIds(['order']);
            },
            function (Type $type) use ($client) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $cartDraft) use ($type) {
                        return $cartDraft->setCustom(CustomFieldObjectDraft::ofType($type->getReference()));
                    },
                    function (Cart $cart) use ($client, $type) {
                        $value = CartFixture::uniqueCartString();

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                SetCustomFieldAction::ofName('testField')
                                    ->setValue($value)
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame($value, $result->getCustom()->getFields()->getTestField());

                        return $result;
                    }
                );
            }
        );
    }

    public function testLineItemCustomType()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())->setResourceTypeIds(['line-item']);
            },
            function (Type $type) use ($client) {
                ProductFixture::withProduct(
                    $client,
                    function (Product $product) use ($client, $type) {
                        CartFixture::withUpdateableCart(
                            $client,
                            function (Cart $cart) use ($client, $type, $product) {
                                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                            $product->getId(),
                                            $variant->getId(),
                                            1
                                        )
                                    );
                                $response = $this->execute($client, $request);
                                $cart = $request->mapFromResponse($response);

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartSetLineItemCustomTypeAction::ofTypeKey($type->getKey())
                                            ->setLineItemId($cart->getLineItems()->current()->getId())
                                    );
                                $response = $this->execute($client, $request);
                                $result = $request->mapFromResponse($response);

                                $this->assertSame(
                                    $type->getId(),
                                    $result->getLineItems()->current()->getCustom()->getType()->getId()
                                );

                                return $result;
                            }
                        );
                    }
                );
            }
        );
    }

    public function testAddLineItemWithCustomType()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())->setResourceTypeIds(['line-item']);
            },
            function (Type $type) use ($client) {
                ProductFixture::withProduct(
                    $client,
                    function (Product $product) use ($client, $type) {
                        CartFixture::withUpdateableCart(
                            $client,
                            function (Cart $cart) use ($client, $product, $type) {
                                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                            $product->getId(),
                                            $variant->getId(),
                                            1
                                        )->setCustom(
                                            CustomFieldObjectDraft::ofTypeKeyAndFields(
                                                $type->getKey(),
                                                FieldContainer::of()->setTestField('1')
                                            )
                                        )
                                    )
                                    ->addAction(
                                        CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                            $product->getId(),
                                            $variant->getId(),
                                            1
                                        )->setCustom(
                                            CustomFieldObjectDraft::ofTypeKeyAndFields(
                                                $type->getKey(),
                                                FieldContainer::of()->setTestField('2')
                                            )
                                        )
                                    );
                                $response = $this->execute($client, $request);
                                $result = $request->mapFromResponse($response);

                                $this->assertCount(2, $result->getLineItems());
                                $this->assertSame(1, $result->getLineItems()->getAt(0)->getQuantity());
                                $this->assertSame(1, $result->getLineItems()->getAt(1)->getQuantity());

                                return $result;
                            }
                        );
                    }
                );
            }
        );
    }

    public function testCustomLineItemCustomType()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())
                    ->setResourceTypeIds(['custom-line-item']);
            },
            function (Type $type) use ($client) {
                TaxCategoryFixture::withTaxCategory(
                    $client,
                    function (TaxCategory $taxCategory) use ($client, $type) {
                        CartFixture::withUpdateableCart(
                            $client,
                            function (Cart $cart) use ($client, $type, $taxCategory) {
                                $item = 'item-' . CartFixture::uniqueCartString();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartAddCustomLineItemAction::ofNameQuantityMoneySlugAndTaxCategory(
                                            LocalizedString::ofLangAndText('en', $item),
                                            1,
                                            Money::ofCurrencyAndAmount('EUR', 100),
                                            $item,
                                            $taxCategory->getReference()
                                        )
                                    );
                                $response = $this->execute($client, $request);
                                $cart = $request->mapFromResponse($response);

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartSetCustomLineItemCustomTypeAction::ofTypeKey($type->getKey())
                                            ->setCustomLineItemId($cart->getCustomLineItems()->current()->getId())
                                    );
                                $response = $this->execute($client, $request);
                                $result = $request->mapFromResponse($response);

                                $this->assertSame(
                                    $type->getId(),
                                    $result->getCustomLineItems()->current()->getCustom()->getType()->getId()
                                );

                                return $result;
                            }
                        );
                    }
                );
            }
        );
    }

    public function testLineItemCustomField()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())
                    ->setResourceTypeIds(['line-item']);
            },
            function (Type $type) use ($client) {
                ProductFixture::withProduct(
                    $client,
                    function (Product $product) use ($client, $type) {
                        CartFixture::withUpdateableCart(
                            $client,
                            function (Cart $cart) use ($client, $type, $product) {
                                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                            $product->getId(),
                                            $variant->getId(),
                                            1
                                        )->setCustom(CustomFieldObjectDraft::ofType($type->getReference()))
                                    );
                                $response = $this->execute($client, $request);
                                $cart = $request->mapFromResponse($response);

                                $value = CartFixture::uniqueCartString();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartSetLineItemCustomFieldAction::ofName('testField')
                                            ->setLineItemId($cart->getLineItems()->current()->getId())
                                            ->setValue($value)
                                    );
                                $response = $this->execute($client, $request);
                                $result = $request->mapFromResponse($response);

                                $this->assertSame(
                                    $value,
                                    $result->getLineItems()->current()->getCustom()->getFields()->getTestField()
                                );

                                return $result;
                            }
                        );
                    }
                );
            }
        );
    }

    public function testCustomLineItemCustomField()
    {
        $client = $this->getApiClient();

        TypeFixture::withDraftType(
            $client,
            function (TypeDraft $typeDraft) {
                return $typeDraft->setKey('key-' . TypeFixture::uniqueTypeString())
                    ->setResourceTypeIds(['custom-line-item']);
            },
            function (Type $type) use ($client) {
                TaxCategoryFixture::withTaxCategory(
                    $client,
                    function (TaxCategory $taxCategory) use ($client, $type) {
                        CartFixture::withUpdateableCart(
                            $client,
                            function (Cart $cart) use ($client, $type, $taxCategory) {
                                $name  = 'item-' . CartFixture::uniqueCartString();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartAddCustomLineItemAction::ofNameQuantityMoneySlugAndTaxCategory(
                                            LocalizedString::ofLangAndText('en', $name),
                                            1,
                                            Money::ofCurrencyAndAmount('EUR', 100),
                                            $name,
                                            $taxCategory->getReference()
                                        )->setCustom(CustomFieldObjectDraft::ofType($type->getReference()))
                                    );
                                $response = $this->execute($client, $request);
                                $cart = $request->mapFromResponse($response);

                                $value = CartFixture::uniqueCartString();

                                $request = RequestBuilder::of()->carts()->update($cart)
                                    ->addAction(
                                        CartSetCustomLineItemCustomFieldAction::ofName('testField')
                                            ->setCustomLineItemId($cart->getCustomLineItems()->current()->getId())
                                            ->setValue($value)
                                    );
                                $response = $this->execute($client, $request);
                                $result = $request->mapFromResponse($response);

                                $this->assertSame(
                                    $value,
                                    $result->getCustomLineItems()->current()->getCustom()->getFields()->getTestField()
                                );

                                return $result;
                            }
                        );
                    }
                );
            }
        );
    }

    public function testPayment()
    {
        $client = $this->getApiClient();

        PaymentFixture::withPayment(
            $client,
            function (Payment $payment) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $payment) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartAddPaymentAction::of()->setPayment($payment->getReference()));
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(
                            $payment->getId(),
                            $cart->getPaymentInfo()->getPayments()->current()->getId()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRemovePaymentAction::of()->setPayment($payment->getReference()));
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertNull($result->getPaymentInfo());

                        return $result;
                    }
                );
            }
        );
    }

    public function testDiscountCode()
    {
        $client = $this->getApiClient();

        DiscountCodeFixture::withDraftDiscountCode(
            $client,
            function (DiscountCodeDraft $discountCodeDraft) {
                return $discountCodeDraft->setIsActive(true);
            },
            function (DiscountCode $discountCode) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $discountCode) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartAddDiscountCodeAction::ofCode($discountCode->getCode()));
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(
                            $discountCode->getId(),
                            $cart->getDiscountCodes()->current()->getDiscountCode()->getId()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRemoveDiscountCodeAction::ofDiscountCode($discountCode->getReference()));
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertEmpty($result->getDiscountCodes());

                        return $result;
                    }
                );
            }
        );
    }
// todo cartdiscount draft e amche qui personalizzata
    public function testDiscountCodeCustomPredicate()
    {
        $type = $this->getType('key-' . $this->getTestRun(), 'order');
        $cartDraft = $this->getDraft();
        $cartDraft->setCustom(
            CustomFieldObjectDraft::ofTypeAndFields(
                $type->getReference(),
                FieldContainer::of()->set('testField', $this->getTestRun())
            )
        );
        $cartDraft->setLineItems(
            LineItemDraftCollection::of()
                ->add(LineItemDraft::ofProductIdVariantIdAndQuantity($this->getProduct()->getId(), 1, 1))
        );

        $cart = $this->createCart($cartDraft);

        $cartDiscountDraft = CartDiscountDraft::ofNameValuePredicateTargetOrderActiveAndDiscountCode(
            LocalizedString::ofLangAndText('en', 'test-' . $this->getTestRun() . '-discount'),
            AbsoluteCartDiscountValue::of()->setMoney(
                MoneyCollection::of()->add(Money::ofCurrencyAndAmount('EUR', 100))
            ),
            'custom.testField = "' . $this->getTestRun() . '"',
            LineItemsTarget::of()->setPredicate('1=1'),
            '0.9' . trim((string)mt_rand(1, TestHelper::RAND_MAX), '0'),
            true,
            true
        );
        $request = CartDiscountCreateRequest::ofDraft($cartDiscountDraft);
        $response = $request->executeWithClient($this->getClient());
        $cartDiscount = $request->mapFromResponse($response);
        TestHelper::getInstance($this->getClient())->setCartDiscount($cartDiscount);
        $discountCode = $this->getDiscountCode();

        $request = CartUpdateRequest::ofIdAndVersion($cart->getId(), $cart->getVersion())
            ->addAction(CartAddDiscountCodeAction::ofCode($discountCode->getCode()))
        ;
        $response = $request->executeWithClient($this->getClient());
        $cart = $request->mapResponse($response);
        $this->deleteRequest->setVersion($cart->getVersion());

        $this->assertSame($discountCode->getId(), $cart->getDiscountCodes()->current()->getDiscountCode()->getId());

        $this->assertSame(
            $cartDiscount->getId(),
            $cart->getLineItems()->current()
                ->getDiscountedPricePerQuantity()->current()
                ->getDiscountedPrice()->getIncludedDiscounts()->current()
                ->getDiscount()->getId()
        );
    }
// todo cartdiscount draft e amche qui personalizzata
    public function testMultiBuyDiscount()
    {


        $draft = $this->getDraft();
        $draft->setLineItems(
            LineItemDraftCollection::of()
                ->add(LineItemDraft::ofProductIdVariantIdAndQuantity($this->getProduct()->getId(), 1, 3))
        );

        $cart = $this->createCart($draft);

        $draft = CartDiscountDraft::ofNameValuePredicateTargetOrderActiveAndDiscountCode(
            LocalizedString::ofLangAndText('en', 'test-' . $this->getTestRun() . '-discount'),
            RelativeCartDiscountValue::of()->setPermyriad(10000),
            '1=1',
            MultiBuyLineItemsTarget::ofPredicateTriggerDiscountedAndMode(
                '1=1',
                3,
                1,
                MultiBuyLineItemsTarget::MODE_CHEAPEST
            ),
            '0.9' . trim((string)mt_rand(1, TestHelper::RAND_MAX), '0'),
            true,
            true
        );
        $request = CartDiscountCreateRequest::ofDraft($draft);
        $response = $request->executeWithClient($this->getClient());
        TestHelper::getInstance($this->getClient())->setCartDiscount($request->mapResponse($response));

        $discountCode = $this->getDiscountCode();

        $request = CartUpdateRequest::ofIdAndVersion($cart->getId(), $cart->getVersion())
            ->addAction(CartAddDiscountCodeAction::ofCode($discountCode->getCode()))
        ;
        $response = $request->executeWithClient($this->getClient());
        $cart = $request->mapResponse($response);
        $this->deleteRequest->setVersion($cart->getVersion());

        $this->assertSame($discountCode->getId(), $cart->getDiscountCodes()->current()->getDiscountCode()->getId());

        $this->assertSame(
            TestHelper::getInstance($this->getClient())->getCartDiscount()->getId(),
            $cart->getLineItems()->current()
                ->getDiscountedPricePerQuantity()->current()
                ->getDiscountedPrice()->getIncludedDiscounts()->current()
                ->getDiscount()->getId()
        );
        $this->assertSame(
            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount() * 2,
            $cart->getLineItems()->current()->getTotalPrice()->getCentAmount()
        );
    }
// todo cartdiscount draft e amche qui personalizzata
    public function testMultiBuyCustomLineItemDiscount()
    {
        $draft = $this->getDraft();
        $draft->setCustomLineItems(
            CustomLineItemDraftCollection::of()
                ->add(
                    CustomLineItemDraft::ofNameMoneySlugTaxCategoryAndQuantity(
                        LocalizedString::ofLangAndText('en', 'Test'),
                        Money::ofCurrencyAndAmount('EUR', 1000),
                        'test',
                        $this->getTaxCategory()->getReference(),
                        3
                    )
                )
        );

        $cart = $this->createCart($draft);

        $draft = CartDiscountDraft::ofNameValuePredicateTargetOrderActiveAndDiscountCode(
            LocalizedString::ofLangAndText('en', 'test-' . $this->getTestRun() . '-discount'),
            RelativeCartDiscountValue::of()->setPermyriad(10000),
            '1=1',
            MultiBuyCustomLineItemsTarget::ofPredicateTriggerDiscountedAndMode(
                '1=1',
                3,
                1,
                MultiBuyCustomLineItemsTarget::MODE_CHEAPEST
            ),
            '0.9' . trim((string)mt_rand(1, TestHelper::RAND_MAX), '0'),
            true,
            true
        );
        $request = CartDiscountCreateRequest::ofDraft($draft);
        $response = $request->executeWithClient($this->getClient());
        TestHelper::getInstance($this->getClient())->setCartDiscount($request->mapResponse($response));

        $discountCode = $this->getDiscountCode();

        $request = CartUpdateRequest::ofIdAndVersion($cart->getId(), $cart->getVersion())
            ->addAction(CartAddDiscountCodeAction::ofCode($discountCode->getCode()))
        ;
        $response = $request->executeWithClient($this->getClient());
        $cart = $request->mapResponse($response);
        $this->deleteRequest->setVersion($cart->getVersion());

        $this->assertSame($discountCode->getId(), $cart->getDiscountCodes()->current()->getDiscountCode()->getId());

        $this->assertSame(
            TestHelper::getInstance($this->getClient())->getCartDiscount()->getId(),
            $cart->getCustomLineItems()->current()
                ->getDiscountedPricePerQuantity()->current()
                ->getDiscountedPrice()->getIncludedDiscounts()->current()
                ->getDiscount()->getId()
        );
        $this->assertSame(
            $cart->getCustomLineItems()->current()->getMoney()->getCentAmount() * 2,
            $cart->getCustomLineItems()->current()->getTotalPrice()->getCentAmount()
        );
    }
// todo cartdiscount draft e amche qui personalizzata
    public function testDiscountCodeCustomLineItemPredicate()
    {
        $type = $this->getType('key-' . $this->getTestRun(), 'line-item');
        $draft = $this->getDraft();
        $draft->setLineItems(
            LineItemDraftCollection::of()
                ->add(
                    LineItemDraft::ofProductIdVariantIdAndQuantity($this->getProduct()->getId(), 1, 1)
                        ->setCustom(
                            CustomFieldObject::of()
                                ->setType($type->getReference())
                                ->setFields(FieldContainer::of()->set('testField', $this->getTestRun()))
                        )
                )
        );

        $cart = $this->createCart($draft);

        $draft = CartDiscountDraft::ofNameValuePredicateTargetOrderActiveAndDiscountCode(
            LocalizedString::ofLangAndText('en', 'test-' . $this->getTestRun() . '-discount'),
            CartDiscountValue::of()->setType('absolute')->setMoney(
                MoneyCollection::of()->add(Money::ofCurrencyAndAmount('EUR', 100))
            ),
            '1=1',
            CartDiscountTarget::of()->setType('lineItems')->setPredicate('custom.testField = "' . $this->getTestRun() . '"'),
            '0.9' . trim((string)mt_rand(1, TestHelper::RAND_MAX), '0'),
            true,
            true
        );
        $request = CartDiscountCreateRequest::ofDraft($draft);
        $response = $request->executeWithClient($this->getClient());
        TestHelper::getInstance($this->getClient())->setCartDiscount($request->mapResponse($response));

        $discountCode = $this->getDiscountCode();

        $request = CartUpdateRequest::ofIdAndVersion($cart->getId(), $cart->getVersion())
            ->addAction(CartAddDiscountCodeAction::ofCode($discountCode->getCode()))
        ;
        $response = $request->executeWithClient($this->getClient());
        $cart = $request->mapResponse($response);
        $this->deleteRequest->setVersion($cart->getVersion());

        $this->assertSame($discountCode->getId(), $cart->getDiscountCodes()->current()->getDiscountCode()->getId());

        $this->assertSame(
            TestHelper::getInstance($this->getClient())->getCartDiscount()->getId(),
            $cart->getLineItems()->current()
                ->getDiscountedPricePerQuantity()->current()
                ->getDiscountedPrice()->getIncludedDiscounts()->current()
                ->getDiscount()->getId()
        );
    }

    public function localeProvider()
    {
        return [
            ['en', 'en'],
            ['de', 'de'],
            ['de-de', 'de-DE'],
            ['de-DE', 'de-DE'],
            ['de_de', 'de-DE'],
            ['de_DE', 'de-DE'],
        ];
    }

    /**
     * @dataProvider localeProvider
     */
    public function testLocale($locale, $expectedLocale)
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client, $locale, $expectedLocale) {
                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetLocaleAction::ofLocale($locale));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame($expectedLocale, $result->getLocale());

                return $result;
            }
        );
    }

    public function testCustomerGroup()
    {
        $client = $this->getApiClient();

        CustomerGroupFixture::withCustomerGroup(
            $client,
            function (CustomerGroup $customerGroup) use ($client) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $cartDraft) use ($customerGroup) {
                        return $cartDraft->setCustomerGroup($customerGroup->getReference());
                    },
                    function (Cart $cart) use ($client, $customerGroup) {
                        $this->assertSame($customerGroup->getId(), $cart->getCustomerGroup()->getId());

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartSetCustomerGroupAction::of());
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertNotNull($cart->getCustomerGroup());

                        return $result;
                    }
                );
            }
        );
    }

    public function invalidLocaleProvider()
    {
        return [
            ['en-en'],
            ['en_en'],
            ['en_EN'],
            ['en-EN'],
            ['fr'],
        ];
    }

    /**
     * @dataProvider invalidLocaleProvider
     */
    public function testInvalidLocale($locale)
    {
        $this->expectException(FixtureException::class);
        $this->expectExceptionCode(400);

        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client, $locale) {
                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetLocaleAction::ofLocale($locale));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                return $result;
            }
        );
    }

    public function testTaxRoundingMode()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client) {
                $this->assertSame(Cart::TAX_ROUNDING_MODE_HALF_EVEN, $cart->getTaxRoundingMode());

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartChangeTaxRoundingModeAction::ofTaxRoundingMode(Cart::TAX_ROUNDING_MODE_HALF_DOWN));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame(Cart::TAX_ROUNDING_MODE_HALF_DOWN, $result->getTaxRoundingMode());

                return $result;
            }
        );
    }

    public function testCreateWithTaxRoundingMode()
    {
        $client = $this->getApiClient();

        CartFixture::withDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setTaxRoundingMode(Cart::TAX_ROUNDING_MODE_HALF_DOWN);
            },
            function (Cart $cart) use ($client) {
                $this->assertSame(Cart::TAX_ROUNDING_MODE_HALF_DOWN, $cart->getTaxRoundingMode());
            }
        );
    }

    public function testTaxCalculationModeUnitPrice()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableCart(
            $client,
            function (Cart $cart) use ($client) {
                $this->assertSame(Cart::TAX_CALCULATION_MODE_LINE_ITEM_LEVEL, $cart->getTaxCalculationMode());

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartChangeTaxCalculationModeAction::ofTaxCalculationMode(Cart::TAX_CALCULATION_MODE_UNIT_PRICE_LEVEL));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame(Cart::TAX_CALCULATION_MODE_UNIT_PRICE_LEVEL, $result->getTaxCalculationMode());

                return $result;
            }
        );
    }

    public function testCreateWithTaxCalculationMode()
    {
        $client = $this->getApiClient();

        CartFixture::withDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setTaxCalculationMode(Cart::TAX_CALCULATION_MODE_UNIT_PRICE_LEVEL);
            },
            function (Cart $cart) use ($client) {
                $this->assertSame(Cart::TAX_CALCULATION_MODE_UNIT_PRICE_LEVEL, $cart->getTaxCalculationMode());
            }
        );
    }


    public function testAutomaticDelete()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setDeleteDaysAfterLastModification(1);
            },
            function (Cart $cart) use ($client) {
                $this->assertSame(1, $cart->getDeleteDaysAfterLastModification());

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartSetDeleteDaysAfterLastModificationAction::ofDays(2));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame(2, $result->getDeleteDaysAfterLastModification());

                return $result;
            }
        );
    }

    public function testPriceTiersOnAddLineItem()
    {
        $client = $this->getApiClient();

        ProductFixture::withDraftProduct(
            $client,
            function (ProductDraft $productDraft) {
                $productDraft->getMasterVariant()->getPrices()->current()->setTiers(PriceTierCollection::of()
                    ->add(
                        PriceTier::of()->setValue(Money::ofCurrencyAndAmount('EUR', 10))->setMinimumQuantity(2)
                    )
                    ->add(
                        PriceTier::of()->setValue(Money::ofCurrencyAndAmount('EUR', 1))->setMinimumQuantity(3)
                    ));

                return $productDraft;
            },
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateabledraftCart(
                    $client,
                    function (CartDraft $cartDraft) use ($product, $variant) {
                        $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                        return $cartDraft->setLineItems(
                            LineItemDraftCollection::of()
                                ->add(LineItemDraft::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                ))
                        );
                    },
                    function (Cart $cart) use ($client, $product, $variant) {
                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            $product->getProductType()->getId(),
                            $cart->getLineItems()->current()->getProductType()->getId()
                        );
                        $this->assertSame(
                            100,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                $product->getId(),
                                $variant->getId(),
                                1
                            ));
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($product->getId(), $cart->getLineItems()->current()->getProductId());
                        $this->assertSame(
                            $product->getProductType()->getId(),
                            $cart->getLineItems()->current()->getProductType()->getId()
                        );
                        $this->assertSame(
                            10,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartChangeLineItemQuantityAction::ofLineItemIdAndQuantity(
                                $cart->getLineItems()->current()->getId(),
                                3
                            ));
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(3, $cart->getLineItems()->current()->getQuantity());
                        $this->assertSame(
                            1,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRemoveLineItemAction::ofLineItemId(
                                $cart->getLineItems()->current()->getId()
                            )->setQuantity(1));
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(2, $cart->getLineItems()->current()->getQuantity());
                        $this->assertSame(
                            10,
                            $cart->getLineItems()->current()->getPrice()->getValue()->getCentAmount()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartRemoveLineItemAction::ofLineItemId(
                                $cart->getLineItems()->current()->getId()
                            ));
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertCount(0, $result->getLineItems());

                        return $result;
                    }
                );
            }
        );
    }
//todo cart discount draft
    public function testGiftLineItem()
    {
        $cartDiscount = $this->getGiftLineItemCartDiscount();

        $product = $this->getProduct();
        $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

        $draft = $this->getDraft();
        $cart = $this->createCart($draft);

        $request = CartUpdateRequest::ofIdAndVersion($cart->getId(), $cart->getVersion())
            ->addAction(
                CartAddLineItemAction::ofProductIdVariantIdAndQuantity($product->getId(), $variant->getId(), 1)
            );

        $response = $request->executeWithClient($this->getClient());
        $cart = $request->mapResponse($response);
        $this->deleteRequest->setVersion($cart->getVersion());

        $this->assertCount(2, $cart->getLineItems());

        $this->assertSame(100, $cart->getTotalPrice()->getCentAmount());

        $giftLineItemIncluded = false;
        foreach ($cart->getLineItems() as $lineItem) {
            $this->assertSame($product->getReference()->getId(), $lineItem->getProductId());
            $this->assertSame($variant->getId(), $lineItem->getVariant()->getId());
            if ($lineItem->getLineItemMode() == LineItem::LINE_ITEM_MODE_GIFT_LINE_ITEM) {
                $giftLineItemIncluded = true;
                $this->assertSame(0, $lineItem->getTotalPrice()->getCentAmount());
                $this->assertCount(1, $lineItem->getDiscountedPricePerQuantity());
                $this->assertSame(
                    $cartDiscount->getId(),
                    $lineItem->getDiscountedPricePerQuantity()->current()
                            ->getDiscountedPrice()->getIncludedDiscounts()->current()
                            ->getDiscount()->getId()
                );
            }
        }
        $this->assertTrue($giftLineItemIncluded);
    }

    public function testSetLineItemTaxAmount()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($variant) {
                        return $draft
                            ->setLineItems(LineItemDraftCollection::of()->add(LineItemDraft::ofSku($variant->getSku())))
                            ->setTaxMode(Cart::TAX_MODE_EXTERNAL_AMOUNT);
                    },
                    function (Cart $cart) use ($client, $variant, $product) {
                        $taxAmount = mt_rand(1, 100000);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemTaxAmountAction::of()
                                    ->setLineItemId($cart->getLineItems()->current()->getId())
                                    ->setExternalTaxAmount(
                                        ExternalTaxAmountDraft::ofTotalGrossAndTaxRate(
                                            Money::ofCurrencyAndAmount('EUR', $taxAmount),
                                            ExternalTaxRateDraft::ofNameCountryAndAmount('test', 'DE', 1.0)
                                        )
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            $taxAmount,
                            $result->getLineItems()->current()->getTaxedPrice()->getTotalGross()->getCentAmount()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testSetCustomLineItemTaxAmount()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setCustomLineItems(
                    CustomLineItemDraftCollection::of()
                        ->add(
                            CustomLineItemDraft::ofNameMoneySlugAndQuantity(
                                LocalizedString::ofLangAndText('en', 'test'),
                                Money::ofCurrencyAndAmount('EUR', 100),
                                'test-124',
                                1
                            )
                        )
                )->setTaxMode(Cart::TAX_MODE_EXTERNAL_AMOUNT);
            },
            function (Cart $cart) use ($client) {
                $taxAmount = mt_rand(1, 100000);

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(
                        CartSetCustomLineItemTaxAmountAction::of()
                            ->setCustomLineItemId($cart->getCustomLineItems()->current()->getId())
                            ->setExternalTaxAmount(
                                ExternalTaxAmountDraft::ofTotalGrossAndTaxRate(
                                    Money::ofCurrencyAndAmount('EUR', $taxAmount),
                                    ExternalTaxRateDraft::ofNameCountryAndAmount('test', 'DE', 1.0)
                                )
                            )
                    );
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $this->assertSame(
                    $taxAmount,
                    $result->getCustomLineItems()->current()->getTaxedPrice()->getTotalGross()->getCentAmount()
                );

                return $result;
            }
        );
    }

    public function testSetShippingMethodTaxAmount()
    {
        $client = $this->getApiClient();

        ShippingMethodFixture::withShippingMethod(
            $client,
            function (ShippingMethod $shippingMethod, Zone $zone) use ($client) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($shippingMethod, $zone) {
                        return $draft->setShippingAddress(
                            Address::of()->setCountry('DE')->setState($zone->getLocations()->current()->getState())
                        )->setShippingMethod($shippingMethod->getReference())
                            ->setTaxMode(Cart::TAX_MODE_EXTERNAL_AMOUNT);
                    },
                    function (Cart $cart) use ($client, $shippingMethod) {
                        $taxAmount = mt_rand(1, 100000);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetShippingMethodTaxAmountAction::of()
                                    ->setExternalTaxAmount(
                                        ExternalTaxAmountDraft::ofTotalGrossAndTaxRate(
                                            Money::ofCurrencyAndAmount('EUR', $taxAmount),
                                            ExternalTaxRateDraft::ofNameCountryAndAmount('test', 'DE', 1.0)
                                        )
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            $taxAmount,
                            $result->getShippingInfo()->getTaxedPrice()->getTotalGross()->getCentAmount()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testSetTotalTaxAmount()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($variant) {
                        return $draft
                            ->setLineItems(LineItemDraftCollection::of()->add(LineItemDraft::ofSku($variant->getSku())))
                            ->setTaxMode(Cart::TAX_MODE_EXTERNAL_AMOUNT);
                    },
                    function (Cart $cart) use ($client) {
                        $taxAmount = mt_rand(1, 100000);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetCartTotalTaxAction::of()
                                    ->setExternalTotalGross(
                                        Money::ofCurrencyAndAmount('EUR', $taxAmount)
                                    )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame($taxAmount, $result->getTaxedPrice()->getTotalGross()->getCentAmount());

                        return $result;
                    }
                );
            }
        );
    }

    public function testSetShippingRateInput()
    {
        $client = $this->getApiClient();

        ProjectFixture::withProject(
            $client,
            function (Project $project) use ($client) {
                CartFixture::withUpdateableCart(
                    $client,
                    function (Cart $cart) use ($client, $project) {
                        $this->assertInstanceOf(Project::class, $project);

                        $request = RequestBuilder::of()->project()->update($project)
                            ->addAction(
                                ProjectSetShippingRateInputTypeAction::of()
                                    ->setShippingRateInputType(CartScoreType::of())
                            );
                        $response = $this->execute($client, $request);
                        $request->mapFromResponse($response);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetShippingRateInputAction::of()
                                    ->setShippingRateInput(ScoreShippingRateInput::ofScore(1))
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertInstanceOf(ScoreShippingRateInput::class, $result->getShippingRateInput());
                        $this->assertSame(1, $result->getShippingRateInput()->getScore());

                        return $result;
                    }
                );
            }
        );
    }

    public function testCartAddLineItemWithShippingDetailsAndApplyDelta()
    {
        $client = $this->getApiClient();

        ProductFixture::withProduct(
            $client,
            function (Product $product) use ($client) {
                $variant = $product->getMasterData()->getCurrent()->getMasterVariant();

                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) {
                        return $draft->setItemShippingAddresses(
                            AddressCollection::of()->add(
                                Address::of()->setCountry('DE')->setKey('key1')
                            )
                        );
                    },
                    function (Cart $cart) use ($client, $product, $variant) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartAddLineItemAction::ofProductIdVariantIdAndQuantity(
                                    $product->getId(),
                                    $variant->getId(),
                                    1
                                )
                                    ->setShippingDetails(ItemShippingDetailsDraft::ofTargets(
                                        ItemShippingTargetCollection::of()
                                            ->add(ItemShippingTarget::of()
                                                ->setQuantity(10)->setAddressKey('key1'))
                                    ))
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $itemShippingAddresses = $cart->getItemShippingAddresses();
                        $addressKey = $cart->getLineItems()->current()->getShippingDetails()
                                            ->getTargets()->current()->getAddressKey();

                        $this->assertInstanceOf(AddressCollection::class, $itemShippingAddresses);
                        $this->assertSame('DE', $itemShippingAddresses->current()->getCountry());
                        $this->assertSame('key1', $itemShippingAddresses->current()->getKey());
                        $this->assertSame('key1', $addressKey);

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartApplyDeltaToLineItemShippingDetailsTargetsAction::of()
                                    ->setLineItemId($cart->getLineItems()->current()->getId())
                                    ->setTargetsDelta(ItemShippingTargetCollection::of()->add(
                                        ItemShippingTarget::of()->setQuantity(15)->setAddressKey('key1')
                                    ))
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame(
                            'key1',
                            $cart->getLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getAddressKey()
                        );
                        $this->assertSame(
                            25,
                            $cart->getLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getQuantity()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartSetLineItemShippingDetailsAction::ofLineItemIdAndShippingDetails(
                                    $cart->getLineItems()->current()->getId(),
                                    ItemShippingDetailsDraft::ofTargets(
                                        ItemShippingTargetCollection::of()->add(
                                            ItemShippingTarget::of()->setQuantity(20)->setAddressKey('key1')
                                        )
                                    )
                                )
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            'key1',
                            $result->getLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getAddressKey()
                        );
                        $this->assertSame(
                            20,
                            $result->getLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getQuantity()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testCartAddItemShippingAddressAction()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setItemShippingAddresses(
                    AddressCollection::of()->add(Address::of()->setCountry('DE')->setKey('key1'))
                );
            },
            function (Cart $cart) use ($client) {
                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartAddItemShippingAddressAction::of()
                        ->setAddress(Address::of()->setKey('key2')->setCountry('US')));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $itemShippingAddresses = $result->getItemShippingAddresses();

                $this->assertInstanceOf(AddressCollection::class, $itemShippingAddresses);
                $this->assertSame(2, $itemShippingAddresses->count());
                $this->assertSame('US', $itemShippingAddresses->getAt(1)->getCountry());
                $this->assertSame('key2', $itemShippingAddresses->getAt(1)->getKey());

                return $result;
            }
        );
    }

    public function testCartRemoveItemShippingAddressAction()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setItemShippingAddresses(
                    AddressCollection::of()
                        ->add(Address::of()->setCountry('DE')->setKey('key1'))
                        ->add(Address::of()->setCountry('US')->setKey('key2'))
                );
            },
            function (Cart $cart) use ($client) {
                $itemShippingAddresses = $cart->getItemShippingAddresses();

                $this->assertInstanceOf(AddressCollection::class, $itemShippingAddresses);
                $this->assertSame(2, $itemShippingAddresses->count());

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartRemoveItemShippingAddressAction::of()->setAddressKey('key1'));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $itemShippingAddresses = $result->getItemShippingAddresses();

                $this->assertSame(1, $itemShippingAddresses->count());
                $this->assertSame('US', $itemShippingAddresses->current()->getCountry());
                $this->assertSame('key2', $itemShippingAddresses->current()->getKey());

                return $result;
            }
        );
    }

    public function testCartUpdateItemShippingAddressAction()
    {
        $client = $this->getApiClient();

        CartFixture::withUpdateableDraftCart(
            $client,
            function (CartDraft $draft) {
                return $draft->setItemShippingAddresses(
                    AddressCollection::of()
                        ->add(Address::of()->setCountry('DE')->setKey('key1'))
                );
            },
            function (Cart $cart) use ($client) {
                $itemShippingAddresses = $cart->getItemShippingAddresses();
                $this->assertInstanceOf(AddressCollection::class, $itemShippingAddresses);
                $this->assertSame(1, $itemShippingAddresses->count());
                $this->assertSame('DE', $itemShippingAddresses->getAt(0)->getCountry());

                $request = RequestBuilder::of()->carts()->update($cart)
                    ->addAction(CartUpdateItemShippingAddressAction::of()->setAddress(
                        Address::of()->setCountry('US')->setKey('key1')
                    ));
                $response = $this->execute($client, $request);
                $result = $request->mapFromResponse($response);

                $itemShippingAddresses = $result->getItemShippingAddresses();

                $this->assertSame(1, $itemShippingAddresses->count());
                $this->assertSame('US', $itemShippingAddresses->current()->getCountry());
                $this->assertSame('key1', $itemShippingAddresses->current()->getKey());

                return $result;
            }
        );
    }

    public function testCustomLineItemSetShippingDetailsAndApplyDelta()
    {
        $client = $this->getApiClient();
        $name = LocalizedString::ofLangAndText('en', 'test-' . CartFixture::uniqueCartString());

        TaxCategoryFixture::withTaxCategory(
            $client,
            function (TaxCategory $taxCategory) use ($client, $name) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($taxCategory, $name) {
                        $customLineItem = CustomLineItemDraft::ofNameMoneySlugTaxCategoryAndQuantity(
                            $name,
                            Money::ofCurrencyAndAmount('EUR', 100),
                            $name->en,
                            $taxCategory->getReference(),
                            1
                        );
                        return $draft->setCustomLineItems(CustomLineItemDraftCollection::of()->add($customLineItem));
                    },
                    function (Cart $cart) use ($client, $taxCategory, $name) {
                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartAddItemShippingAddressAction::of()
                                ->setAddress(Address::of()->setKey('key1')->setCountry('US')))
                            ->addAction(
                                CartSetCustomLineItemShippingDetailsAction::ofCustomLineItemIdAndShippingDetails(
                                    $cart->getCustomLineItems()->current()->getId(),
                                    ItemShippingDetailsDraft::ofTargets(
                                        ItemShippingTargetCollection::of()->add(
                                            ItemShippingTarget::of()->setQuantity(10)->setAddressKey('key1')
                                        )
                                    )
                                )
                            );
                        $response = $this->execute($client, $request);
                        $cart = $request->mapFromResponse($response);

                        $this->assertSame($name->en, $cart->getCustomLineItems()->current()->getName()->en);
                        $this->assertSame(
                            'key1',
                            $cart->getCustomLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getAddressKey()
                        );
                        $this->assertSame(
                            10,
                            $cart->getCustomLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getQuantity()
                        );

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(
                                CartApplyDeltaToCustomLineItemShippingDetailsTargetsAction::of()
                                    ->setCustomLineItemId($cart->getCustomLineItems()->current()->getId())
                                    ->setTargetsDelta(ItemShippingTargetCollection::of()->add(
                                        ItemShippingTarget::of()->setQuantity(20)->setAddressKey('key1')
                                    ))
                            );
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertSame(
                            'key1',
                            $result->getCustomLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getAddressKey()
                        );
                        $this->assertSame(
                            30,
                            $result->getCustomLineItems()->current()->getShippingDetails()
                                ->getTargets()->current()->getQuantity()
                        );

                        return $result;
                    }
                );
            }
        );
    }

    public function testUpdateAndDeleteForCartInStore()
    {
        $client = $this->getApiClient();

        StoreFixture::withStore(
            $client,
            function (Store $store) use ($client) {
                CartFixture::withUpdateableDraftCart(
                    $client,
                    function (CartDraft $draft) use ($store) {
                        return $draft->setStore(StoreReference::ofKey($store->getKey()));
                    },
                    function (Cart $cart) use ($client, $store) {
                        $email = 'test-' . CartFixture::uniqueCartString() . '@example.com';

                        $request = RequestBuilder::of()->carts()->update($cart)
                            ->addAction(CartSetCustomerEmailAction::of()->setEmail($email))
                            ->inStore($store->getKey());
                        $response = $this->execute($client, $request);
                        $result = $request->mapFromResponse($response);

                        $this->assertInstanceOf(Cart::class, $result);
                        $this->assertSame($cart->getId(), $result->getId());
                        $this->assertSame($email, $result->getCustomerEmail());
                        $this->assertSame(
                            'in-store/key=' . $store->getKey() . '/carts/' . $result->getId(),
                            (string)$request->httpRequest()->getUri()
                        );
                        $this->assertSame($store->getKey(), $result->getStore()->getKey());

                        $cartRequest = RequestBuilder::of()->carts()->delete($result);
                        $request = InStoreRequestDecorator::ofStoreKeyAndRequest($store->getKey(), $cartRequest);
                        $response = $request->executeWithClient($this->getClient());
                        $result = $request->mapResponse($response);

                        $this->assertInstanceOf(Cart::class, $result);
                        $this->assertSame($cart->getId(), $result->getId());

                        return $result;
                    }
                );
            }
        );
    }

    /**
     * @return CartDraft
     */
    protected function getDraft()
    {
        $draft = CartDraft::ofCurrency('EUR')->setCountry('DE');

        return $draft;
    }

    protected function createCart(CartDraft $draft)
    {
        $request = CartCreateRequest::ofDraft($draft);
        $response = $request->executeWithClient($this->getClient());
        $cart = $request->mapResponse($response);

        $this->deleteRequest = CartDeleteRequest::ofIdAndVersion($cart->getId(), $cart->getVersion());
        $this->cleanupRequests[] = $this->deleteRequest;

        return $cart;
    }
}
