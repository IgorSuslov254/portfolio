<?php

/**
 * Deleting an order viadelivery
 * 
 * class viadeliveryShippingCancelPackage
 * @author Suslov Igor <igirsuslov@gmail.com>
 */
class viadeliveryShippingCancelPackage extends viadeliveryShippingPlugin
{
    /**
     * Deleting an order viadelivery
     * 
     * @param waOrder $order
     * @return array($original_track_number, $tracking_number, $view_data)
     */
    public function cancelPackage(waOrder $order)
    {

        if ( empty( $this->vid->getSettings('shop_id') ) || empty( $this->vid->getSettings('security_token') ) || empty( $order ) ){
            waLog::dump($this->vid->_w('required'), 'ErrosViadelivery.log');
            return null;
        }

        $options = [
            'net' => [
                'format' => waNet::FORMAT_JSON,
                'verify' => false
            ],
            'url' => 'https://insales.viadelivery.pro/webhook/delete?id='.$this->vid->getSettings('shop_id').'&sid=@'.$this->vid->getSettings('security_token')
        ];

        $content = [
            "number" => $order->id_str,
            "fulfillment_status" => "declined",
            "delivery_info" => [   
                "shipping_company_handle" => "Via.Delivery"
            ]
        ];

        $net = new waNet($options['net']);

        try {
            $result = $net->query($options['url'], $content, waNet::METHOD_POST);
        } catch (Exception $e) {
            waLog::dump($e->getMessage(), 'ErrosViadelivery.log');
            return null;
        }

        if ($result['status'] == 'OK'){

            $data['view_data'] = $this->vid->_w('delete_success');
        } else{

            waLog::dump($this->vid->_w('delete_error'), 'ErrosViadelivery.log');

            $data['view_data'] = $this->vid->_w('delete_error');
        }

        $data['original_track_number'] = null;
        $data['tracking_number'] = null;

        return $data;
    }
}