<?php
class Cronphp
{
    protected function __construct() { }
    protected function __clone() { }

    private function array_of_values_from_cronelement($cronelement, $element_type, $minvalue, $maxvalue)
    {
        $values = array();    
        $month_translate = array("JAN" => 1, "FEB" => 2, "MAR" => 3, "APR" => 4, "MAY" => 5, "JUN" => 6, "JUL" => 7, "AUG" => 8, "SEP" => 9, "OCT" => 10, "NOV" => 11, "DEC" => 12);
        $weekday_translate = array("MON" => 1, "DIE" => 2, "MIT" => 3, "DON" => 4, "FRI" => 5, "SAT" => 6, "SUN" => 7);

        $separator_pos = strpos($cronelement, "/") or $separator_pos=strlen($cronelement);
        $cronelement_value = substr($cronelement, 0, $separator_pos);
        $cronelement_interval = substr($cronelement, $separator_pos+1);
        $cronelement_interval = max(1, $cronelement_interval);

        if ($cronelement_value == '*')
        {
            $cronelement_value_array = array();
            if ($element_type == 'DOW'){$maxvalue=6;}
            for ($u = $minvalue; $u < $maxvalue+1; $u++)
            {
                $cronelement_value_array[] = $u;
            }
        }
        else
        {
            $cronelement_value_array = explode(",", $cronelement_value, 1000);
        }

        foreach($cronelement_value_array as $cronelement_values)
        {
            $cronelement_values = strtoupper($cronelement_values);
            $values_separator_pos = strpos($cronelement_values, "-") or $values_separator_pos=strlen($cronelement_values);
            $from_value_pre = substr($cronelement_values, 0, $values_separator_pos);
            $to_value_pre = substr($cronelement_values, $values_separator_pos+1);
            if ($element_type == "MONTH")
            {
                if (isset($month_translate[$from_value_pre])){$from_value = $month_translate[$from_value_pre];}
                else{$from_value=$from_value_pre;}
                if (isset($month_translate[$to_value_pre])){$to_value = $month_translate[$to_value_pre];}
                else{$to_value=$to_value_pre or $to_value=-1;}
            }
            elseif ($element_type == "DOW")
            {
                if (isset($weekday_translate[$from_value_pre])){$from_value = $weekday_translate[$from_value_pre];}
                else{$from_value=$from_value_pre;}
                if (isset($weekday_translate[$to_value_pre])){$to_value = $weekday_translate[$to_value_pre];}
                else{$to_value=$to_value_pre or $to_value=-1;}
            }
            else
            {
                $from_value =$from_value_pre; 
                $to_value = $to_value_pre or $to_value=-1;   
            }

            $from_value = min($from_value, $maxvalue);
            $from_value = max($from_value, $minvalue);

            if ($to_value > $from_value)
            {
                $to_value = min($to_value, $maxvalue);
                for ($i = $from_value; $i < $to_value+1; $i++)
                {
                    if (($i % $cronelement_interval) == 0){$values[]=intval($i);}
                }
            }
            else 
            {
                if (($from_value % $cronelement_interval) == 0){$values[]=intval($from_value);}
            }
        }
        return $values;
    }

    public static function getmatch($cronstring, $input_unixtime = null)
    {
        if ($cronstring =="@yearly")  {$cronstring = "0 0 1 1 *";}
        if ($cronstring =="@annually"){$cronstring = "0 0 1 1 *";}
        if ($cronstring =="@monthly") {$cronstring = "0 0 1 * *";}
        if ($cronstring =="@weekly")  {$cronstring = "0 0 * * 0";}
        if ($cronstring =="@daily")   {$cronstring = "0 0 * * *";}
        if ($cronstring =="@hourly")  {$cronstring = "0 * * * *";}

        if (!$input_unixtime){$input_unixtime = time();}
        $cronstring = trim($cronstring);
        if (preg_match("/  /", $cronstring)> 0){return "\nERROR: check your syntax\n";}
        if (substr_count($cronstring, " ") < 4){return "\nERROR: check your syntax\n";}
        $cronstring_array = explode(" ", $cronstring, 5);
        
        $input_minute = intval(date("i", $input_unixtime));
        $input_hour = date("G", $input_unixtime);
        $input_day_of_the_month = date("j", $input_unixtime);
        $input_month = date("n", $input_unixtime);
        $input_day_of_the_week = date("w", $input_unixtime);


        $cron_minute_arr = self::array_of_values_from_cronelement($cronstring_array[0], "MINUTE", 0, 59);
        $cron_hour_arr = self::array_of_values_from_cronelement($cronstring_array[1], "HOUR", 0, 23);
        $cron_day_of_the_month_arr = self::array_of_values_from_cronelement($cronstring_array[2], "DOM", 1, 31);
        $cron_month_arr = self::array_of_values_from_cronelement($cronstring_array[3], "MONTH", 1, 12);
        $cron_day_of_the_week_arr = self::array_of_values_from_cronelement($cronstring_array[4], "DOW", 0, 7);

        $match=true;
        if (!in_array($input_minute, $cron_minute_arr)){$match = false;}
        if (!in_array($input_hour, $cron_hour_arr)){$match = false;}
        if ($cronstring_array[2] == "*") // DOW strict match if DOM ="*"
        {
            if (!in_array($input_day_of_the_week, $cron_day_of_the_week_arr))
            {$match = false;}
        }
        if ($cronstring_array[4] == "*") // DOM strict match if DOW ="*"
        {
            if (!in_array($input_day_of_the_month, $cron_day_of_the_month_arr))
            {$match = false;}
        }
        if (!in_array($input_day_of_the_week, $cron_day_of_the_week_arr) &&
            !in_array($input_day_of_the_month, $cron_day_of_the_month_arr))
            {$match = false;}

        if (!in_array($input_month, $cron_month_arr)){$match = false;}

        return $match;
    } 
}
?>

