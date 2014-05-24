<?php

/*
 * This file is part of HTMLPurifier Bundle.
 * (c) 2012 Maxime Dizerens
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array(
	'encoding' => 'UTF-8',
    'finalize' => true,
    'preload'  => false,
    'settings' => array(
        'default' => array(
            'HTML.Doctype'             => 'HTML 4.01 Strict',
            'HTML.Allowed'             => 'br,p,strong,em,ul,ol,li',
            'CSS.AllowedProperties'    => '',
            'Cache.SerializerPath'     => storage_path(),
        ),
    ),
);
