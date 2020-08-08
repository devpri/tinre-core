<?php

namespace Devpri\Tinre\Jobs;

use DeviceDetector\DeviceDetector;
use Devpri\Tinre\Models\Click;
use Devpri\Tinre\Models\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessClick implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    protected $createdAt;

    protected $ip;

    protected $userAgent;

    protected $referer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Url $url, $createdAt, $ip, $userAgent, $referer)
    {
        $this->url = $url;
        $this->createdAt = $createdAt;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->referer = $referer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device = new DeviceDetector($this->userAgent);
        $device->discardBotInformation();
        $device->parse();

        if ($device->isBot()) {
            return;
        }

        $osInfo = $device->getOs() ?? null;
        $client = $device->getClient() ?? null;
        $location = geoip()->getLocation($this->ip);

        Click::create([
            'url_id' => $this->url->id,
            'country' => $location->getAttribute('iso_code') ?? null,
            'region' => $location->getAttribute('state_name') ?? null,
            'city' => $location->getAttribute('city') ?? null,
            'device_type' => $device->getDeviceName() ? $device->getDeviceName() : null,
            'device_brand' => $device->getBrandName() ? $device->getBrandName() : null,
            'device_model' => $device->getModel() ? $device->getModel() : null,
            'user_agent' => $this->userAgent,
            'os' => $osInfo['short_name'] ?? null,
            'browser' => $client['name'] ?? null,
            'referer' => $this->referer,
            'referer_host' => parse_url($this->referer, PHP_URL_HOST),
            'created_at' => $this->createdAt,
        ]);

        $this->url->timestamps = false;
        $this->url->increment('total_clicks');
    }
}
