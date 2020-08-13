<?php

namespace Devpri\Tinre\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class StatsService
{
    protected $driver;

    public function __construct()
    {
        $connection = config('database.default');
        $this->driver = config("database.connections.{$connection}.driver");
    }

    public function getClicks(int $urlId, array $date): array
    {
        $dates = DB::table('clicks')
            ->select(DB::raw("{$this->getDateFromat($date)} as label"), DB::raw('count(*) as value'))
            ->where('url_id', $urlId)
            ->whereBetween('created_at', $date)
            ->groupBy('label')
            ->get()
            ->toArray();

        return $dates;
    }

    public function getData(string $column, int $urlId, array $date): object
    {
        $dbFunction = $this->driver === 'pgsql' ? 'COALESCE' : 'IFNULL';

        $data = DB::table('clicks')->where('url_id', $urlId)
            ->select(DB::raw("{$dbFunction}({$column}, 'UNK') as label"), DB::raw('count(*) as value'))
            ->whereBetween('created_at', $date)
            ->groupBy('label')
            ->orderBy('value', 'DESC')
            ->paginate(10);

        return $data;
    }

    protected function getDateFromat(array $date): string
    {
        $startDate = Carbon::parse($date[0]);
        $endDate = Carbon::parse($date[1]);

        $diff = $startDate->diffInDays($endDate);

        $dateFormat = '%Y-%m-%d';
        $pgsqlDateFormat = 'YYYY-MM-DD';

        if ($diff === 0) {
            $dateFormat = '%Y-%m-%d %H:00';
            $pgsqlDateFormat = 'YYYY-MM-DD HH24:00';
        }

        if ($diff > 90) {
            $dateFormat = '%Y-%m';
            $pgsqlDateFormat = 'YYYY-MM';
        }

        if ($diff > 1092) {
            $dateFormat = '%Y';
            $pgsqlDateFormat = 'YYYY';
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
