<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BotController extends Controller
{

    public function index(Request $request)
    {
        $ip = ($request->ip  == '') ? $request->ip() : $request->ip;
        $useragent = ($request->ua == '') ? $request->userAgent()   : $request->ua;

      

        if (Cache::has('bot_' . $ip)) {
            $response = Cache::get('bot_' . $ip);
            $response['cache'] = true;
            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        } else {
            $hostname = gethostbyaddr($ip);
            $ip2country = $this->ip2country($ip);
            $isp = $ip2country->isp;

            $ipcheck = $this->checkIP($ip);
            $useragentcheck = $this->checkUserAgent($useragent);
            $hostnamecheck = $this->checkHostname($hostname);
            $ispcheck = $this->checkISP($isp);
            if ($ipcheck) {
                $reason = "Bot IP Detected";
                $is_bot = true;
            } elseif ($useragentcheck) {
                $reason = "Bot User Agent Detected";
                $is_bot = true;
            } elseif ($hostnamecheck) {
                $reason = "Bot Hostname Detected";
                $is_bot = true;
            } elseif ($ispcheck) {
                $reason = "Bot ISP Detected";
                $is_bot = true;
            } else {
                $reason = 'OK';
                $is_bot = false;
            }

            $response['success'] = true;
            $response['ip'] = $ip;
            $response['useragent'] = $useragent;
            $response['ua'] = $useragent;
            $response['hostname'] = $hostname;
            $response['isp'] = $isp;
            $response['is_bot'] = $is_bot;
            $response['why'] = $reason;
            $response['reason'] = $reason;
            $response['ip2country'] = $ip2country;
            Cache::put('bot_' . $ip, $response, 60 * 24);
            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        }
    }
    public function ip2country($ip)
    {
        $model = \App\Models\Ip2country::where('ip', $ip)->first();
        if ($model) {
            return $model->makeHidden(['id', 'updated_at']);
        } else {
            $endpoint = "https://pro.ip-api.com/json/" . $ip . "?key=" . env('IPAPI_KEY');
            $http = new \GuzzleHttp\Client;

            $response = $http->get($endpoint);
            $response = json_decode($response->getBody());
            $ip2country = new \App\Models\Ip2country;
            $ip2country->as = $response->as;
            $ip2country->city = $response->city;
            $ip2country->country = $response->country;
            $ip2country->countryCode = $response->countryCode;
            $ip2country->isp = $response->isp;
            $ip2country->lat = $response->lat;
            $ip2country->lon = $response->lon;
            $ip2country->org = $response->org;
            $ip2country->query = $response->query;
            $ip2country->region = $response->region;
            $ip2country->regionName = $response->regionName;
            $ip2country->status = $response->status;
            $ip2country->timezone = $response->timezone;
            $ip2country->zip = $response->zip;
            $ip2country->ip = $response->query;
            $ip2country->save();

            return $response;
        }
    }
    public function ip2countryJson(Request $request)
    {
        $ip = ($request->ip  == '') ? $request->ip() : $request->ip;
        $ip2country = $this->ip2country($ip);
        return response()->json($ip2country, 200, [], JSON_PRETTY_PRINT);
    }
    public function checkIP($ip)
    {
        $ip = \App\Models\Ip::where('content', $ip)->first();
        if ($ip) {
            return true;
        }
        return false;
    }

    public function checkUserAgent($ua)
    {
        $ua = \App\Models\Useragent::where('content', $ua)->first();
        if ($ua) {
            return true;
        }
        return false;
    }
    public function checkHostname($host)
    {
        $host = \App\Models\Hostname::where('content', $host)->first();
        if ($host) {
            return true;
        }
        return false;
    }
    public function checkISP($isp)
    {
        $isp = \App\Models\Isp::where('content', $isp)->first();
        if ($isp) {
            return true;
        }
        return false;
    }


    public function stats()
    {
        $ipsCount = \App\Models\Ip::count();
        $useragentsCount = \App\Models\Useragent::count();
        $hostnamesCount = \App\Models\Hostname::count();
        $ispsCount = \App\Models\Isp::count();
        $ip2countriesCount = \App\Models\Ip2country::count();
        $response['success'] = true;
        $response['total_ip'] = $ipsCount;
        $response['total_useragent'] = $useragentsCount;
        $response['total_hostname'] = $hostnamesCount;
        $response['total_isp'] = $ispsCount;
        $response['total_ip2country'] = $ip2countriesCount;
        $response['number_format'] = [
            'total_ip' => number_format($ipsCount),
            'total_useragent' => number_format($useragentsCount),
            'total_hostname' => number_format($hostnamesCount),
            'total_isp' => number_format($ispsCount),
            'total_ip2country' => number_format($ip2countriesCount),
        ];
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }
}