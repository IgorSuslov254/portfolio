<?php

/**
 * 
 */
class viadeliveryShippingModel extends waModel
{
	protected $table;

	public function getRegionName($code = null)
	{
		if ($code == null) return false;

		$this->table = 'wa_region';

		$result = $this->getByField('code', $code);
		return $result['name'];
	}

	public function getPoints(array $params = [])
	{
		if (empty($params)) return false;

		$d = new DateTime();

		$this->table = 'wa_shipping_viadelivery_points';

		$result = $this->query("
			SELECT
			    *
			FROM
			    ".$this->table." 
			WHERE
			    region = s:region AND city = s:city AND date > s:date
		", array(
			'region' => $params['region'],
			'city' => $params['city'],
			'date' => $d->format('Y-m-d H:i:s')
		));

		$data = [];

		if (!empty($result)){
			foreach ($result as $row) {
				$data[] = $row;	
			}
		}

		if (empty($data)){
			return false;
		} else{
			return json_decode($data[0]['data'], true);
		} 
	}

	public function setPoints($params)
	{
		if (empty($params)) return false;

		$d = new DateTime();

		$this->table = 'wa_shipping_viadelivery_points';

		$result = $this->query("
			SELECT
			    *
			FROM
			    ".$this->table." 
			WHERE
			    region = s:region AND city = s:city
		", array(
			'region' => $params['region'],
			'city' => $params['city']
		));

		$data = [];

		if (!empty($result)){
			foreach ($result as $row) {
				$data[] = $row;	
			}
		}

		$d->add(new DateInterval('PT4H'));

		if (empty($data)){
			$this->query("
				INSERT
			    ".$this->table."(`region`, `city`, `data`, `date`)
			VALUE
			    (
			        s:region,
			        s:city,
			        s:data,
			        s:date
			    )
			", array(
				'region' => $params['region'],
				'city' 	=> $params['city'],
				'data' 	=> $params['data'],
				'date' 	=> $d->format('Y-m-d H:i:s')
			));

		} else{
			$this->query("
				UPDATE
				    ".$this->table."
				SET
				    `region` = s:region,
				    `city` 	= s:city,
				    `data` 	= s:data,
				    `date` 	= s:date
				WHERE
				    `id` = i:id
			", array(
				'region' => $params['region'],
				'city' 	=> $params['city'],
				'data' 	=> $params['data'],
				'date' 	=> $d->format('Y-m-d H:i:s'),
				'id' 	=> $data[0]['id']
			));
		}
	}

}