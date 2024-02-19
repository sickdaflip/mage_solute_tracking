<?php
class Solute_Tracking_Block_Frontend_Checkout_Success_Tracking extends Mage_Core_Block_Template
{

    private $_hasDigitalProducts = false;

    private $_hasBackOrder = false;

    public $order;

    /**
     * Get the order and customer information.
     */
    protected function _construct()
    {
        $this->order = $this->getOrder();

        $this->_prepareOrderData();

        parent::_construct();
    }

    /**
     * Should be called before the page has rendered.
     * Spin through all the order items and work out if:
     * - The order contains digital products.
     * - The order contains an item which is on back order.
     */
    private function _prepareOrderData()
    {
        foreach( $this->getOrderItems() as $item )
        {
            if( $item->getProductType() == 'virtual' )
                $this->_hasDigitalProducts = true;

            if( $item->getQtyBackordered() )
                $this->_hasBackOrder = true;
        }
    }

    /**
     * Does the order contain digital products?
     *
     * @return bool
     */
    public function doesOrderContainDigitalProducts()
    {
        return $this->_hasDigitalProducts;
    }

    /**
     * Does the order contain a back order?
     *
     * @return bool
     */
    public function doesOrderContainBackOrder()
    {
        $containsBackOrder = false;

        foreach( $this->getOrderItems() as $item )
        {
            if( $item->getQtyBackordered() )
                $containsBackOrder = true;
        }

        return $containsBackOrder;
    }

    /**
     * Format a number for google.
     *
     * @param $number
     *
     * @return string
     */
    public function formatNumberForGoogle( $number )
    {
        return number_format( $number, 2, '.', '' );
    }

    /**
     * Gets the items price in a format google expects.
     *
     * @param $item
     *
     * @return string
     */
    public function getPriceForItem( $item )
    {
        return $this->formatNumberForGoogle( $item->getPrice() );
    }

    /**
     * Get the order total
     */
    public function getOrderTotal()
    {
        return $this->formatNumberForGoogle( $this->order->getSubtotal() );
    }

    /**
     * Get any order discounts applied to the cart.
     *
     * @return float negative
     */
    public function getOrderDiscounts()
    {
        return $this->formatNumberForGoogle( $this->order->getDiscountAmount() );
    }

    /**
     * Get the order shipping amount.
     *
     * @return float
     */
    public function getOrderShipping()
    {
        return $this->formatNumberForGoogle( $this->order->getShippingAmount() );
    }

    /**
     * Get the total amount taxed.
     *
     * @return float
     */
    public function getOrderTax()
    {
        return $this->formatNumberForGoogle( $this->order->getTaxInvoiced() );
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::getModel('sales/order')->loadByIncrementId( Mage::getSingleton('checkout/session')->getLastRealOrderId() );
    }

    /**
     * Get the order id.
     *
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->order->getIncrementId();
    }

    /**
     * Get all the order items on the order
     *
     * @return mixed
     */
    public function getOrderItems()
    {
        return $this->order->getAllVisibleItems();
    }

    /**
     * Get the customer model
     *
     * @return Mage_Customer_Model
     */
    public function getCustomer()
    {
        return Mage::getModel( 'customer/customer' )->load( $this->order->getCustomerId() );
    }

    /**
     * Returns the store's currency code.
     *
     * @return string
     */
    public function getCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get the customer's country
     *
     * @return string
     */
    public function getCustomerCountry()
    {
        return $this->order->getShippingAddress()->getCountry();
    }

}
