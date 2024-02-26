<?php
use joomx\mcs\plugin\helpers\McsLog;


class GeoIP {

    /**
     * @var string
     */
    public $ip;

    /**
     * @param string $ip
     * @return void
     */
    public function __construct($ip = null) {
        // ip
        if (!empty($ip) && $this->isValidIp($ip)) {
            $this->ip = $ip;
        } else {
            $this->ip = $this->getIp();
        }
    }

    /**
     * Получаем данные с сервера или из cookie
     * @param boolean $cookie
     * @return string|array
     */
    public function detectLocation($cookie = true)
    {
        // если используем куки если параметр уже получен, то достаем и возвращаем данные из куки
        if ($cookie && filter_input(INPUT_COOKIE, 'geoip')) {
            return unserialize(filter_input(INPUT_COOKIE, 'geoip'));
        }
        $data = $this->sendRequest($this->ip);
        if (!empty($data)) {
            setcookie('geoip', serialize($data), time() + 3600 * 24 * 7, '/'); //устанавливаем куки на неделю
        }
        return $data;
    }

    /**
     * функция получает данные по ip.
     * @return array - возвращает массив с данными
     */
    protected function sendRequest($ip)
    {
        $url = "http://ip-api.com/json/{$ip}?lang=ru";
        $curl = curl_init($url);
        McsLog::add($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($curl);
        McsLog::add('Original Response: ' . var_export($response, true));
        curl_close($curl);
        return $this->convertResponse($response);
    }


    /**
     * @return array - возвращает массив с данными
     */
    protected function convertResponse($response)
    {
        $data = [];
        $response = @json_decode($response, true);
//      Response example: {
//        "query": "92.255.191.93",
//        "status": "success",
//        "country": "Россия",
//        "countryCode": "RU",
//        "region": "OMS",
//        "regionName": "Омская область",
//        "city": "Омск",
//        "zip": "644000",
//        "lat": 54.9978,
//        "lon": 73.4001,
//        "timezone": "Asia/Omsk",
//        "isp": "CJSC \"ER-Telecom Holding\" Omsk branch",
//        "org": "JSC \"ER-Telecom Holding\" Omsk Branch",
//        "as": "AS41843 JSC ER-Telecom Holding"
//        }
//      OR
//        {
//        "query": "92.255.191.932",
//        "message": "invalid query",
//        "status": "fail"
//        }
        if (empty($response) || !isset($response['city']) || !isset($response['status']) || $response['status'] === 'fail') {
            // чет какая-то фигня случилась
            $data['error'] = 'Not found';
        } else {
            // ну вроде ж все нормас?
            // нам по факту нужно только три вещи: city, province, country
            $data['city'] = $response['city'];
            $data['province'] = !empty($response['regionName']) ? $response['regionName'] : '';
            $data['country'] = !empty($response['country']) ? $response['country'] : '';
        }
        return $data;
    }

    /**
     * функция определяет ip адрес по глобальному массиву $_SERVER
     * ip адреса проверяются начиная с приоритетного, для определения возможного использования прокси
     * @return string ip-адрес
     */
    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($this->isValidIp($ip)) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        return '0.0.0.0';
    }


    /**
     * функция для проверки валидности ip адреса
     * @param ip string адрес в формате 1.2.3.4
     * @return boolean : true - если ip валидный, иначе false
     */
    public function isValidIp($ip)
    {
        return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

}
