<?php 

namespace App\Helpers;

use App\Models\Manufacturer;
use Illuminate\Support\Facades\Redis;

class Helper
{
    static protected $key = 'redisManufacturerId';

    public static function get_current()
    {
        return Redis::get(self::$key);
    }
    public static function set_current(string $manufacturerId)
    {
        return Redis::set(self::$key, $manufacturerId);
    }
    public static function number_format($value)
    {
        return number_format($value, 0, ',', '.');
    }
    public static function select_default()
    {
        $item = Manufacturer::first();
        if($item) {
            self::set_current($item->id);
        }
    }
}