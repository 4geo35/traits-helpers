<?php

namespace Aweram\TraitsHelpers\Helpers;

use Carbon\Carbon;
use Mockery\Exception;

/**
 * @method Carbon|null forFilter($value, $to = false)
 * @method Carbon|null changeTz($value)
 * @method Carbon|null format($value, $format = "d.m.Y H:i")
 */
class DateHelper
{
    const TZ = "Europe/Moscow";
    const UTC = "Etc/UTC";
    const ACTIONS = [
        "forFilter",
        "changeTz",
        "format"
    ];

    public $timeZone;
    public $date;

    public function __construct($timeZone = self::TZ)
    {
        $this->timeZone = $timeZone;
        $this->date = null;
    }

    public function __call($method, $args)
    {
        if (in_array($method, self::ACTIONS))
            $this->methodRouter($method, $args);
        return $this->date;
    }

    public function methodRouter($method, $args): void
    {
        switch ($method) {
            case "forFilter":
                call_user_func_array([$this, "getForFilterDate"], $args);
                break;

            case "changeTz":
                call_user_func_array([$this, "changeTimeZone"], $args);
                break;

            case "format":
                call_user_func_array([$this, "formatValue"], $args);
                break;
        }
    }

    /**
     * Подготовить дату для фильтрации.
     *
     * @param $value
     * @param bool $to
     * @return void
     */
    protected function getForFilterDate($value, bool $to = false): void
    {
        if ($to) $value .= " 23:59:59";
        else $value .= " 00:00:00";
        try {
            $carbon = Carbon::createFromFormat("Y-m-d H:i:s", $value, $this->timeZone);
            $carbon->timezone = self::UTC;
            $this->date = $carbon->toDateTimeString();
        } catch (Exception $ex) {
            $this->date = null;
        }
    }

    /**
     * Изменить часовой пояс.
     *
     * @param $value
     * @return void
     */
    protected function changeTimeZone($value): void
    {
        if (empty($value)) {
            $this->date = $value;
            return;
        }
        try {
            $carbon = new Carbon($value);
        } catch (Exception $ex) {
            $this->date = $value;
            return;
        }
        $carbon->timezone = $this->timeZone;
        $this->date = $carbon->toDateTimeString();
    }

    /**
     * Форматировать значение.
     *
     * @param $value
     * @param string $format
     * @return void
     */
    protected function formatValue($value, string $format = "d.m.Y H:i"): void
    {
        if (empty($value)) {
            $this->date = $value;
            return;
        }
        $this->date = date($format, strtotime($value));
    }
}
