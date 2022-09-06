<?php

/**
* The class of the delivery module
*
* Is responsible for calculating the price
* The drift package method is called when placing an order and when the order status changes to paid
* The cancel Package method is called when the order is returned
*
* Class viadeliveryShipping
* @author Suslov Igor <igirsuslov@gmail.com>
*/
class viadeliveryShipping extends waShipping
{
    /**
     * Tracking and receiving the order status
     * 
     * @param string $tracking_id
     * @return string
     */
    public function tracking($tracking_id = null)
    {   
        if ($tracking_id === null)
            return null;

        return (new viadeliveryShippingGetTracking($this))->getTracking($tracking_id);
    }

    /**
     * creating a custom view of settings
     * 
     * @param array $params
     * @return string
     */
    public function getSettingsHTML($params = array())
    {

        $view = wa()->getView();

         $view->assign(
            array(
                'values' => $this->getSettings(),
                'p' => $this,
            )
        );

        $html = '';
        $html .= $view->fetch($this->path.'/templates/settings.html');
        $html .= parent::getSettingsHTML($params);
        return $html;
    }

    /**
     * Calculation of the cost of delivery and its withdrawal
     * Mandatory method
     *
     * @return array
     */
    protected function calculate()
    {
        $params = [
            'region'    => $this->getAddress()['region'],
            'city'      => $this->getAddress()['city']
        ];

        $slc = new waSerializeCache($params["region"].$params["city"], 1440, "viadelivery");

        if ($slc->isCached()){
            return $slc->get();
        } else {
            $vsgp = new viadeliveryShippingGetPoints($this);
            $get_points = $vsgp->getPoints();

            $points = !empty($get_points) ? $get_points : [
                'rate' => null,
                'comment' => $this->_w('no_delivery')
            ];

            $slc->set($points);

            return $points;
        }     
    }

    /**
     * Creates a draft in via.delivery dashboard
     *
     * @param waOrder $order
     * @return array
     */
    public function draftPackage(waOrder $order, $shipping_data = array())
    {
        return (new viadeliveryShippingDraftPackage($this))->draftPackage($order);
    }

    /**
     * Deletes a draft in via.delivery dashboard
     *
     * @param waOrder $order
     * @return array
     */
    public function cancelPackage(waOrder $order, $shipping_data = array())
    {
        return (new viadeliveryShippingCancelPackage($this))->cancelPackage($order);
    }

    /**
     * @return string
     */
    public function allowedCurrency()
    {
        return 'RUB';
    }

    /**
     * @return string
     */
    public function allowedWeightUnit()
    {
        return 'g';
    }

    /**
     * @return string
     */
    public function allowedLinearUnit()
    {
        return 'm';
    }

    /**
     * Required fields for each type of delivery
     *
     * @param array $service
     * @return array
     */
    public function requestedAddressFieldsForService($service)
    {
        $fields = [];

        $fields = [
            'city'    => [
                'cost'     => true,
                'required' => true,
            ]
        ];

        return null;
    }

    ###############
    # MAKE PUBLIC #
    ###############

    public function getPackageProperty($property)
    {
        return parent::getPackageProperty($property);
    }

    public function getTotalSize()
    {
        return parent::getTotalSize();
    }

    public function getTotalHeight()
    {
        return parent::getTotalHeight();
    }

    public function getTotalWidth()
    {
        return parent::getTotalWidth();
    }

    public function getTotalLength()
    {
        return parent::getTotalLength();
    }

    public function getTotalWeight()
    {
        return parent::getTotalWeight();
    }

    public function getTotalPrice()
    {
        return parent::getTotalPrice();
    }

    public function getTotalRawPrice()
    {
        return parent::getTotalRawPrice();
    }

    public function getAddress($field = NULL)
    {
        return parent::getAddress();
    }
}