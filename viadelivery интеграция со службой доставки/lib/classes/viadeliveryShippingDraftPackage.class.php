<?php

/**
 * creating an order viadelivery
 * 
 * class viadeliveryShippingDraftPackage
 * @author Suslov Igor <igirsuslov@gmail.com>
 */
class viadeliveryShippingDraftPackage extends viadeliveryShippingPlugin
{
    /**
     * creating an order viadelivery
     * 
     * @param waOrder $order
     * @return array($original_track_number, $tracking_number, $view_data)
     */
    public function draftPackage(waOrder $order)
    {
        if ($this->vid->getSettings('noCreateOrderVd') == '1') return null;
        
        $paid = $order->paid_datetime;

        if ( empty($paid) && $this->vid->getSettings('paid') == '1' ) return null;

        if ( empty( $this->vid->getSettings('shop_id') ) || empty( $this->vid->getSettings('security_token') ) || empty( $order ) ){
            waLog::dump($this->vid->_w('required'), 'ErrosViadelivery.log');
            return null;
        }

        $options = [
            'net' => [
                'format' => waNet::FORMAT_JSON,
                'verify' => false
            ],
            'url' => 'https://insales.viadelivery.pro/webhook/update?id='.$this->vid->getSettings('shop_id').'&sid='.$this->vid->getSettings('security_token')
        ];

        $content = [
            "id" => $order->id,
            "number" => $order->id_str,
            "fulfillment_status" => "accepted",
            "items_price" => $order->subtotal,
            "delivery_price" => $order->shipping,
            "total_price" => $order->total,
            "currency_code" => $order->currency,
            "delivery_info" => [   
                "shipping_company_handle" => "Via.Delivery",
                "price" => $order->shipping,
                "outlet" => [
                    "description" => $order->shipping_name,
                    "external_id" => str_replace("pickup_", "", $order->shipping_rate_id),
                    "payment_method" => [
                        "PREPAID",
                        "CARD" 
                    ]
                ]
            ]
        ];

        if ( $this->vid->getSelectedPaymentTypes()[0] == 'prepaid' || $this->vid->getSettings('payment_method') == '1' ){ 
            $content['financial_status'] = 'paid';
            $content['paid_at'] = date('c');
        } else{
            $content['financial_status'] = 'pending';
            $content['paid_at'] = null;
        }

        if ( !empty( $order->getContactField('name') ) )
            $content['client']['name'] = $order->getContactField('name');

        if ( !empty( $order->getContactField('email') ) )
            $content['client']['email'] = $order->getContactField('email');

        if ( !empty( $order->getContactField('phone') ) )
            $content['client']['phone'] = $order->getContactField('phone');

        $weight = $this->vid->getSettings('default_weight');

        $content['order_lines'] = [];
        foreach ($order->items as $key => $item) {

            if( !empty( $item['weight'] ) && $item['weight'] != 0.0 ){
                $weight = $item['weight']/1000;
            } else{
                $weight = 0.1;
            }

            $content['order_lines'][] = [
                "vat" => -1,
                "title" => $item['name'],
                "weight" => $weight,
                "dimensions" => $this->vid->getSettings('dimensions_d')."x".$this->vid->getSettings('dimensions_w')."x".$this->vid->getSettings('dimensions_h'),
                "quantity" => $item['quantity'],
                "full_total_price" => $item['price'],
                "product_id" => $item['sku_id']
            ];
        }

        $net = new waNet($options['net']);

        try {
            $result = $net->query($options['url'], $content, waNet::METHOD_POST);
        } catch (Exception $e) {
            waLog::dump($e->getMessage(), 'ErrosViadelivery.log');
            return null;
        }

        if ($result['status'] == 'OK'){

            $data = [
                'original_track_number' => $order->id_str,
                'tracking_number'       => $order->id_str,
                'view_data'             => $this->vid->_w('create_success').' <a href="https://www.viadelivery.pro/shop/orders" target="_blank">'.$order->id_str.'</a>'
            ];
        } else{

            waLog::dump($this->vid->_w('create_error'), 'ErrosViadelivery.log');

            $data['view_data'] = $this->vid->_w('create_error');
        }

        return $data;
    }
}