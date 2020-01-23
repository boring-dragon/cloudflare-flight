<?php
namespace App\Controllers;

use Jinas\Jsonify\Util;
use Phpfastcache\Helper\Psr16Adapter;

class AnalyticsController
{
    public function analytics()
    {
        $response = new Util();

        $defaultDriver = 'Files';
        $Psr16Adapter = new Psr16Adapter($defaultDriver);

        if (!$Psr16Adapter->has('cloudflaredata')) {
            // Setter action
            $client = new \Cloudflare\Api(getenv('CLOUDFLARE_EMAIL'), getenv('CLOUDFLARE_APIKEY'));

            $analytic = new \Cloudflare\Zone\Analytics($client);
            $array = json_decode(json_encode($analytic->dashboard('50515cfef4495e8bb32f79f6b28b1b54')), true);

            $index = $array["result"]["timeseries"][6];
            $Psr16Adapter->set('cloudflaredata', $index, 120); // 2 minutes before cache expire
        } else {
            // Getter action
            $index = $Psr16Adapter->get('cloudflaredata');
        }

        $request = $index["requests"]["all"];
        $bandwidth = $index["bandwidth"]["all"];
        $pageviews = $index["pageviews"]["all"];
        $uniques = $index["uniques"]["all"];


        if (!array_key_exists("MV", $index["bandwidth"]["country"])) {
            $country_bandwidth = null;
        } else {
            $country_bandwidth = $index["bandwidth"]["country"]["MV"];
        }


        $data = [
            'total_request' => $request,
            'total_bandwidth' => $bandwidth,
            'total_pageviews' => $pageviews,
            'total_unique_users' => $uniques,
            'total_bandwidth_local' => $country_bandwidth
        ];

        echo $response->sendResponse($data, 'data retrieved successfully');
    }
}
