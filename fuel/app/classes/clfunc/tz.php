<?php
class ClFunc_Tz
{
	static $regions = array(
		'Africa',
		'America',
		'Antarctica',
		'Arctic',
		'Asia',
		'Atlantic',
		'Australia',
		'Europe',
		'Indian',
		'Pacific',
		'UTC',
	);

	public static function tz_list()
	{
		static $timezones = null;

		if ($timezones === null)
		{
			$timezones = [];

			$sort = null;
			foreach (DateTimeZone::listIdentifiers() as $timezone)
			{
				$region = explode('/', $timezone, 2);
				$timezones[$region[0]][$timezone] = '['.self::GMT_offset($timezone).'] '.self::tz_name($timezone);
			}
			foreach ($timezones as $reg => $tz)
			{
				asort($tz);
				$timezones[$reg] = $tz;
			}

		}

		return $timezones;
	}

	public static function GMT_offset($timezone)
	{
		$now = new DateTime('now', new DateTimeZone('UTC'));
		$now->setTimezone(new DateTimeZone($timezone));
		$offset = $now->getOffset();

		$hours = intval($offset / 3600);
		$minutes = abs(intval($offset % 3600 / 60));
		return 'GMT'.($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '+00:00');
	}

	public static function tz_name($name)
	{
		$names = explode('/', $name, 2);
		$name = (isset($names[1]))? $names[1]:$names[0];
		$name = str_replace('/', ', ', $name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('St ', 'St.', $name);
		return $name;
	}

	public static function tz_chk($timezone)
	{
		try
		{
			$dtz = new DateTimeZone($timezone);
			return true;
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	public static function tz($format, $to_tz = null, $date = null, $from_tz = null)
	{
		$to_tz = ($to_tz)? $to_tz:date_default_timezone_get();
		$from_tz = ($from_tz)? $from_tz:date_default_timezone_get();
		$date = ($date)? $date:'now';

		$dt = new DateTime($date, new DateTimeZone($from_tz));
		$dt->setTimezone(new DateTimeZone($to_tz));

		return $dt->format($format);
	}
}