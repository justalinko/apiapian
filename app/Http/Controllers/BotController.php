<?php

namespace App\Http\Controllers;

use proxycheck\proxycheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BotController extends Controller
{
    public function cacheName($ip, $ua)
    {
        return 'bot_' . sha1($ip . $ua);
    }
    public function index(Request $request)
    {
        $ip = ($request->ip  == '') ? $request->ip() : $request->ip;
        $useragent = ($request->ua == '') ? $request->userAgent()   : $request->ua;



        if (Cache::has($this->cacheName($ip, $useragent))) {
            $response = Cache::get($this->cacheName($ip, $useragent));
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

            $proxycheckapi = $this->proxycheck($ip);
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
            } elseif ($ip2country?->proxy) {
                $reason = "Proxy Detected";
                $is_bot = true;
            } elseif ($ip2country?->hosting) {
                $reason = "Hosting Detected";
                $is_bot = true;
            } elseif($proxycheckapi->block == 'yes' || $proxycheckapi->$ip?->proxy == 'yes')
            {
                $reason = ($proxycheckapi->block_reason == 'na') ? $proxycheckapi->$ip?->type : $proxycheckapi->block_reason;
                $is_bot = true;
            }else
            {
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
            $response['proxycheck'] = $proxycheckapi;
            Cache::put($this->cacheName($ip, $useragent), $response, 60 * 24);
            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        }
    }
    public function ip2country($ip)
    {
        $model = \App\Models\Ip2country::where('ip', $ip)->first();
        if ($model) {
            $data = $model->makeHidden(['id', 'updated_at']);
            $data['proxy'] = $model->isproxy->is_proxy;
            $data['hosting'] = $model->isproxy->is_hosting;
            return (object) $data;
        } else {
            $endpoint = "https://pro.ip-api.com/json/" . $ip . "?fields=status,as,city,country,countryCode,isp,lat,lon,org,query,region,regionName,timezone,zip,hosting,proxy,mobile&key=" . env('IPAPI_KEY');
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

            $isproxy = new \App\Models\Isproxy;
            if($isproxy->where('ip2country_id',$ip2country->id)->where('ip' , $response->query)->count() < 1){
            $isproxy->ip2country_id = $ip2country->id;
            $isproxy->ip = $response->query;
            $isproxy->is_proxy = $response->proxy;
            $isproxy->is_hosting = $response->hosting;
            //$isproxy->is_mobile = $response->mobile;
            $isproxy->save();
            }

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
        $isProxy = \App\Models\Isproxy::count();
        $response['success'] = true;
        $response['total_ip'] = $ipsCount;
        $response['total_useragent'] = $useragentsCount;
        $response['total_hostname'] = $hostnamesCount;
        $response['total_isp'] = $ispsCount;
        $response['total_ip2country'] = $ip2countriesCount;
        $response['total_isproxy'] = $isProxy;
        $response['number_format'] = [
            'total_ip' => number_format($ipsCount),
            'total_useragent' => number_format($useragentsCount),
            'total_hostname' => number_format($hostnamesCount),
            'total_isp' => number_format($ispsCount),
            'total_ip2country' => number_format($ip2countriesCount),
            'total_isproxy' => number_format($isProxy),
        ];
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }


    public function proxycheck($ip)
    {
        $proxycheck_options = 
        [
            'API_KEY' => env('PROXYCHECK_API'), // Your API Key.
            'ASN_DATA' => 1, // Enable ASN data response.
            'DAY_RESTRICTOR' => 7, // Restrict checking to proxies seen in the past # of days.
            'VPN_DETECTION' => 1, // Check for both VPN's and Proxies instead of just Proxies.
            'RISK_DATA' => 1, // 0 = Off, 1 = Risk Score (0-100), 2 = Risk Score & Attack History.
            'INF_ENGINE' => 1, // Enable or disable the real-time inference engine.
            'TLS_SECURITY' => 0, // Enable or disable transport security (TLS).
            'QUERY_TAGGING' => 1, // Enable or disable query tagging.
            'MASK_ADDRESS' => 0, // Anonymises the local-part of an email address (e.g. anonymous@domain.tld)
            'CUSTOM_TAG' => '@PrivApi.Tech', // Specify a custom query tag instead of the default (Domain+Page).
        ];
        $proxycheckModel = \App\Models\Proxycheck::where('ip', $ip)->first();
        if ($proxycheckModel) {
            $data['status'] = 'ok';
            $data[$ip] = json_decode($proxycheckModel->raw);
            $data['block'] = $proxycheckModel->block;
            $data['block_reason'] = $proxycheckModel->block_reason;
            return (object) $data;
        }else{

        $proxycheck = \proxycheck\proxycheck::check($ip, $proxycheck_options);
        $ip2country = \App\Models\Ip2country::where('ip', $ip)->first();
        $proymod = new \App\Models\Proxycheck;
        $proymod->ip2country_id = $ip2country->id;
        $proymod->ip = $ip;
        $proymod->raw = json_encode($proxycheck[$ip]);
        $proymod->block = $proxycheck['block'];
        $proymod->block_reason = $proxycheck['block_reason'];
        $proymod->save();

            return (Object) $proxycheck;
        }
    }
}
