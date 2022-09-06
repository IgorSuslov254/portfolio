<?php

/**
 * @param viadeliveryShipping $vid
 * abstract class viadeliveryShippingPlugin
 * @author Suslov Igor <igirsuslov@gmail.com>
 */
abstract class viadeliveryShippingPlugin
{
	protected $vid;
    
    function __construct(viadeliveryShipping $vid)
    {
        $this->vid = $vid;
    }

}