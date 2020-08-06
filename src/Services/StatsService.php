<?php

namespace Devpri\Tinre\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class StatsService
{
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
        $data = DB::table('clicks')
            ->select(DB::raw("IFNULL({$column}, 'UNK') as label"), DB::raw('count(*) as value'))
            ->where('url_id', $urlId)
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

        if ($diff === 0) {
            $dateFormat = '%Y-%m-%d %H:00';
        }

        if ($diff > 90) {
            $dateFormat = '%Y-%m';
        }
        
        if ($diff > 1092) {
            $dateFormat = '%Y';
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        switch ($driver) {
            case 'mysql':
                return "DATE_FORMAT(created_at, '{$dateFormat}')";
            case 'pgsql':
                return "to_char(created_at, '{$dateFormat}')";
                break;
            case 'sqlite':
                return "strftime('{$dateFormat}', created_at)";
                break;
            default:
                throw new Exception('Unsupported driver.');
        }
    }
}
