<?php

/**
 *  Class for receiving pick-up points
 * 
 *  class viadeliveryShippingGetPoints
 *  @author Suslov Igor <igirsuslov@gmail.com>
 */
class viadeliveryShippingGetPoints extends viadeliveryShippingPlugin
{
    /**
     * getting pick-up points
     * 
     * @return array
     */
    public function getPoints()
    {
        if (
            empty($this->vid->getSettings('API_points')) || 
            empty($this->vid->getSettings('shop_id')) || 
            empty($this->vid->getAddress()['city']) || 
            empty($this->vid->getAddress()['region']) ||
            !empty($this->vid->getSelectedServiceId())
        ) {
            // waLog::dump($this->vid->_w('required'), 'ErrosViadelivery.log');
            return $result = [];
        }

        $options = [
            'net' => [
                'format' => waNet::FORMAT_JSON,
                'verify' => false
            ],
            'url' => $this->vid->getSettings('API_points').'/point-list?id='.$this->vid->getSettings('shop_id').'&city='.urlencode($this->vid->getAddress()['city'])
        ];

        if ( !empty($this->vid->getAddress()['region']) )
            $options['url'] .= '&region='.urlencode( $this->getRegionName($this->vid->getAddress()['region']));

        if( $this->vid->getTotalWeight() == 0.0 || $this->vid->getSettings('default_weight') == '1' ){
            $options['url'] .= '&weight='.$this->vid->getSettings('weight');
        } elseif( $this->vid->getTotalWeight() != 0.0 && $this->vid->getSettings('default_weight') == '0' ){
            $options['url'] .= '&weight='.$this->vid->getTotalWeight();
        }

        if( !empty( $this->vid->getSettings('dimensions_w') ) &&
            !empty( $this->vid->getSettings('dimensions_d') ) && 
            !empty( $this->vid->getSettings('dimensions_h') ) 
        )
            $options['url'] .= '&length='.$this->vid->getSettings('dimensions_d').'&width='.$this->vid->getSettings('dimensions_w').'&height='.$this->vid->getSettings('dimensions_h');

        $net = new waNet($options['net']);

        try {
            $result = $net->query($options['url'], '', waNet::METHOD_GET);
        } catch (Exception $e) {
            if ($e->getCode() != 28){
                waLog::dump($options['url'], 'ErrosViadelivery.log');
                waLog::dump($e->getMessage(), 'ErrosViadelivery.log');
            }

            return $result = [];
        }

        $this->parse_data($result);

        return $result;
    }

    /**
     * get the name of the region
     * 
     * @param int $code
     * @return string name of the region
     */
    private function getRegionName($code = null)
    {
        if ($code == null) return false;

        return (new viadeliveryShippingModel())->getRegionName($code);
    }

    /**
     * parse data
     * 
     * @param array $result
     * @return array $result
     */
    private function parse_data(&$result = null)
    {
        if ($result == null) return $result = [];

        $pickup = [];

        foreach ($result as $key => $result_value) {
            $date_delivery = $date = new DateTime();

            $dateDeliveryEnd = $result_value['delivery_time']+$this->vid->getSettings('addDate');

            $pickup['pickup_'.$result_value['id']] = [
                'service' => $this->vid->getSettings('service'),
                'name' => $result_value['full_address'],
                'est_delivery' => (!empty( $result_value['delivery_time'] )? $date_delivery->add( new DateInterval('P'.$dateDeliveryEnd.'D') )->format('d.m.Y') : ''),
                'delivery_date' => $date_delivery->format('Y-m-d H:i:s'),
                'timezone' => 'UTC',
                'currency' => $result_value['currency'],
                'type' => 'pickup',
                'custom_data' => [
                    'pickup' => [
                        'id' => $result_value['id'],
                        'timezone' => 'Europe/Moscow',
                        'lat' => $result_value['lat'],
                        'lng' => $result_value['lng'],
                        'description' => $result_value['description'],
                        'payment' => [
                            'card' => 1,
                            'cash' => 1,
                            'prepaid' => 1
                        ]
                    ]
                ],
                'rate' => $result_value['price']
            ];

            if( !empty($result_value['working_time_from']) && !empty($result_value['working_time_to']) ){
                
                $weekday = $date->format('w');
                
                for ($i=0; $i < 7; $i++) { 
                    if ($weekday == 7) $weekday = 0;

                    $date_add = 0;
                    if ($i != 0) $date_add = 1;

                    $pickup['pickup_'.$result_value['id']]['custom_data']['pickup']['schedule']['weekdays'][$weekday] = [
                        'type' => 'workday',
                        'start_work' => $date->add( new DateInterval('P'.$date_add.'D') )->format('Y-m-d').' '.$result_value['working_time_from'],
                        'end_work' => $date->format('Y-m-d').' '.$result_value['working_time_to']
                    ];

                    $weekday++;
                }
            }
        }

        $result = $pickup;
    }
}