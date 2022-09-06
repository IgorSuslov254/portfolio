<?php


/**
 * get the delivery status
 * 
 * class viadeliveryShippingGetTracking
 * @author Suslov Igor <igirsuslov@gmail.com>
 */
class viadeliveryShippingGetTracking extends viadeliveryShippingPlugin
{
    private static $status = [];

    
    /**
     * get the delivery status
     * 
     * @param string $tracking_id
     * @return string the delivery status
     */
    public function getTracking($tracking_id = null)
    {

        self::$status = [
            'APPROVED'                                  => $this->vid->_w('APPROVED'),
            'CANCEL_REQUEST'                            => $this->vid->_w('CANCEL_REQUEST'),
            'CANCELLED'                                 => $this->vid->_w('CANCELLED'),
            'CHANGED_ADDRESS'                           => $this->vid->_w('CHANGED_ADDRESS'),
            'CHECKING'                                  => $this->vid->_w('CHECKING'),
            'COMPLECTED_IN_WAREHOUSE'                   => $this->vid->_w('COMPLECTED_IN_WAREHOUSE'),
            'CREATED'                                   => $this->vid->_w('CREATED'),
            'EXPIRING'                                  => $this->vid->_w('EXPIRING'),
            'FIRSTMILE'                                 => $this->vid->_w('FIRSTMILE'),
            'IN_PROGRESS'                               => $this->vid->_w('IN_PROGRESS'),
            'PICKED_UP'                                 => $this->vid->_w('PICKED_UP'),
            'PLACED_IN_CONSOLIDATION_CELL_IN_WAREHOUSE' => $this->vid->_w('PLACED_IN_CONSOLIDATION_CELL_IN_WAREHOUSE'),
            'PLACED_IN_POSTAMAT'                        => $this->vid->_w('PLACED_IN_POSTAMAT'),
            'PRESORTED'                                 => $this->vid->_w('PRESORTED'),
            'PROBLEM'                                   => $this->vid->_w('PROBLEM'),
            'READY_FOR_WITHDRAW_FROM_PICKUP_POINT'      => $this->vid->_w('READY_FOR_WITHDRAW_FROM_PICKUP_POINT'),
            'READY_TO_BE_SHIPPED_FROM_WAREHOUSE'        => $this->vid->_w('READY_TO_BE_SHIPPED_FROM_WAREHOUSE'),
            'RECEIVED_IN_WAREHOUSE_IN_DETAILS'          => $this->vid->_w('RECEIVED_IN_WAREHOUSE_IN_DETAILS'),
            'REJECTED'                                  => $this->vid->_w('REJECTED')
        ];

        $options = [
            'net' => [
                'format' => waNet::FORMAT_JSON,
                'verify' => false
            ],
            'url' => 'https://api.viadelivery.pro/api/get_order_list?sid=@'.$this->vid->getSettings('security_token')
        ];

        $content = [
            $tracking_id
        ];

        $net = new waNet($options['net']);

        try {
            $result = $net->query($options['url'], $content, waNet::METHOD_POST);
        } catch (Exception $e) {
            waLog::dump($e->getMessage(), 'ErrosViadelivery.log');
            return null;
        }

        if ($result['status'] == 'OK'){

            return $this->vid->_w('status')." ".self::$status[$result['data'][0][$tracking_id]['status']];
        } else {

            waLog::dump($this->vid->_w('status_error'), 'ErrosViadelivery.log');
            return $this->vid->_w('status_error');
        }
    }
}