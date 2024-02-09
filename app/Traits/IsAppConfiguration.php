<?php

namespace App\Traits;

use App\Models\AppConfiguration;

trait IsAppConfiguration
{
    public static function getByCode($code) : AppConfiguration | null
    {
        $app_configuration = AppConfiguration::where('code', $code)->first();

        return $app_configuration != null ? $app_configuration : null;
    }

    public function getValue($value)
    {
        switch ($this->type) {
            case  AppConfiguration::STRING_TYPE:

                return $this->parseString($value);

                break;
            case  AppConfiguration::NUMERIC_TYPE:

                return $this->parseNumber($value);

                break;
            case  AppConfiguration::BOOLEAN_TYPE:

                return $this->parseBoolean($value);

                break;
            case  AppConfiguration::ARRAY_TYPE:

                return $this->parseArray($value);

                break;
            case  AppConfiguration::JSON_TYPE:

                return $this->parseJson($value);

                break;
            
            default:
                
                return $value;
                break;
        }
    }

    private function parseString($data) : string
    {
        return strval($data);
    }

    private function parseNumber($data) : float
    {
        return floatval($data);
    }

    private function parseBoolean($data) : bool
    {
        return boolval($data);
    }

    private function parseArray($data) : array
    {
        return explode(AppConfiguration::ARRAY_SEPARATOR, $data);
    }

    private function parseJson($data) : array
    {
        return json_decode($data, true);
    }
}
