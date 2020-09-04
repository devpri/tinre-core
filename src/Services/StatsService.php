<?php

namespace Devpri\Tinre\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StatsService
{
    protected $driver;

    public function __construct()
    {
        $connection = config('database.default');
        $this->driver = config("database.connections.{$connection}.driver");
    }

    public function getClicks(int $urlId, string $startDate, string $endDate): array
    {
        $dates = DB::table('clicks')
            ->select(DB::raw("{$this->getDateFromat($startDate, $endDate)} as label"), DB::raw('count(*) as value'))
            ->where('url_id', $urlId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('label')
            ->get()
            ->toArray();

        return $dates;
    }

    public function getData(string $column, int $urlId, string $startDate, string $endDate): object
    {
        if (! in_array($column, ['country', 'region', 'city', 'device_type', 'device_brand', 'device_model', 'os', 'browser', 'referer', 'referer_host'])) {
            throw ValidationException::withMessages([
                'column' => [__('Unsupported column.')],
            ]);
        }

        $dbFunction = $this->driver === 'pgsql' ? 'COALESCE' : 'IFNULL';

        $data = DB::table('clicks')->where('url_id', $urlId)
            ->select(DB::raw("{$dbFunction}({$column}, 'UNK') as label"), DB::raw('count(*) as value'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('label')
            ->orderBy('value', 'DESC')
            ->paginate(10);

        return $data;
    }

    protected function getDateFromat(string $startDate, string $endDate): string
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $diff = $startDate->diffInDays($endDate);

        if ($diff === 0) {
            $dateFormat = '%Y-%m-%d %H:00';
            $pgsqlDateFormat = 'YYYY-MM-DD HH24:00';
        } elseif ($diff > 1092) {
            $dateFormat = '%Y';
            $pgsqlDateFormat = 'YYYY';
        } elseif ($diff > 90) {
            $dateFormat = '%Y-%m';
            $pgsqlDateFormat = 'YYYY-MM';
        } else {
            $dateFormat = '%Y-%m-%d';
            $pgsqlDateFormat = 'YYYY-MM-DD';
        }

        switch ($this->driver) {
            case 'mysql':
                return "DATE_FORMAT(created_at, '{$dateFormat}')";
            case 'pgsql':
                return "to_char(created_at, '{$pgsqlDateFormat}')";
            case 'sqlite':
                return "strftime('{$dateFormat}', created_at)";
            default:
                throw new Exception('Unsupported driver.');
        }
    }
}
