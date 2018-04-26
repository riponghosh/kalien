<?php

namespace App;

class AccessAPI
{
    public function accessAPI( $data )
    {
        $data_string = json_encode($data["data"]);

        $url = 'http://'.$data['hostname'].':8080';
        if ( ($data['service'] != "") && ($data['service'] != "index") ) {
            $url = sprintf("%s/%s", $url, $data['service']);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string)));
        $result = curl_exec($ch);
        $result_array = json_decode($result, true);

        return($result_array);
    }
}
