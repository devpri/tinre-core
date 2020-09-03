<?php

namespace Devpri\Tinre\Tests\Services;

use Carbon\Carbon;
use Devpri\Tinre\Models\Click;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Validation\ValidationException;

class StatsServiceTest extends TestCase
{
    protected $statsService;

    public function setUp(): void
    {
        parent::setUp();

        $this->statsService = $this->app->make('Devpri\Tinre\Services\StatsService');
    }

    public function test_can_get_clicks()
    {
        $url = factory(Url::class)->create();

        $now = Carbon::now();

        factory(Click::class, 10)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 5)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $stats = $this->statsService->getClicks($url->id, $now->copy()->subDay(), $now->copy()->addDay());

        $this->assertTrue((int) $stats[0]->value === 10);
    }

    public function test_can_get_countries()
    {
        $url = factory(Url::class)->create();

        $now = Carbon::now();

        $clicks = factory(Click::class, 1)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 8)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $stats = $this->statsService->getData('country', $url->id, $now->copy()->subDay(), $now->copy()->addDay());

        $this->assertTrue($clicks->first()->country === $stats[0]->label);
    }

    public function test_can_get_regions()
    {
        $url = factory(Url::class)->create();

        $now = Carbon::now();

        $clicks = factory(Click::class, 1)->create([
            'url_id' => $url->id,
            'created_at' => $now,
        ]);

        factory(Click::class, 8)->create([
            'url_id' => $url->id,
            'created_at' => $now->copy()->subDays(10),
        ]);

        $stats = $this->statsService->getData('region', $url->id, $now->copy()->subDay(), $now->copy()->addDay());

        $this->assertTrue($clicks->first()->region === $stats[0]->label);
    }

    public function test_can_get_unsupported_column()
    {
        $this->expectException(ValidationException::class);

        $url = factory(Url::class)->create();

        $now = Carbon::now();

        $this->statsService->getData('test', $url->id, $now->copy()->subDay(), $now->copy()->addDay());
    }
}
