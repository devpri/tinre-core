<?php

namespace Devpri\Tinre\Tests\Services;

use Carbon\Carbon;
use Devpri\Tinre\Models\Url;
use Devpri\Tinre\Models\User;
use Devpri\Tinre\Tests\TestCase;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UrlServiceTest extends TestCase
{
    protected $urlService;

    public function setUp(): void
    {
        parent::setUp();

        $this->urlService = $this->app->make('Devpri\Tinre\Services\UrlService');
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $config = $app->get('config');

        $config->set('tinre.restricted_domains', ['devpri.com']);
    }

    public function test_user_can_get_own_urls()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $urls = $this->urlService->index([], $user);

        $this->assertCount(5, $urls);
    }

    public function test_user_cant_get_other_urls()
    {
        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('user')->create();

        factory(Url::class, 3)->create(['user_id' => $user->id]);
        factory(Url::class, 2)->create(['user_id' => $secondUser->id]);

        $urls = $this->urlService->index([], $user);

        $this->assertCount(3, $urls);
    }

    public function test_editor_can_get_all_urls()
    {
        $user = factory(User::class)->states('editor')->create();
        $secondUser = factory(User::class)->states('user')->create();

        factory(Url::class, 3)->create(['user_id' => $user->id]);
        factory(Url::class, 2)->create(['user_id' => $secondUser->id]);

        $urls = $this->urlService->index([], $user);

        $this->assertCount(5, $urls);
    }

    public function test_administrator_can_get_all_urls()
    {
        $user = factory(User::class)->states('administrator')->create();
        $secondUser = factory(User::class)->states('user')->create();

        factory(Url::class, 3)->create(['user_id' => $user->id]);
        factory(Url::class, 2)->create(['user_id' => $secondUser->id]);

        $urls = $this->urlService->index([], $user);

        $this->assertCount(5, $urls);
    }

    public function test_can_get_active_urls()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class)->create(['user_id' => $user->id]);
        factory(Url::class, 5)->create(['user_id' => $user->id, 'active' => 0]);

        $urls = $this->urlService->index(['active' => 1], $user);

        $this->assertCount(1, $urls);
    }

    public function test_can_get_disabled_urls()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class)->create(['user_id' => $user->id]);
        factory(Url::class, 5)->create(['user_id' => $user->id, 'active' => 0]);

        $urls = $this->urlService->index(['active' => 0], $user);

        $this->assertCount(5, $urls);
    }

    public function test_can_get_urls_by_user_id()
    {
        $user = factory(User::class)->states('editor')->create();
        $secondUser = factory(User::class)->states('user')->create();

        factory(Url::class, 4)->create(['user_id' => $user->id]);
        factory(Url::class, 6)->create(['user_id' => $secondUser->id]);

        $urls = $this->urlService->index([
            'user_id' => $secondUser->id,
        ], $user);

        $this->assertCount(6, $urls);
    }

    public function test_can_search_url()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class)->create(['user_id' => $user->id, 'path' => 'test1000134']);
        factory(Url::class, 5)->create(['user_id' => $user->id]);

        $urls = $this->urlService->index(['search' => 'test1000134'], $user);

        $this->assertCount(1, $urls);
    }

    public function test_can_filter_urls_by_date()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class)->create(['user_id' => $user->id, 'path' => 'test10001']);
        factory(Url::class, 5)->create(['user_id' => $user->id, 'created_at' => Carbon::today()->subDays(10)]);

        $urls = $this->urlService->index(['start_date' => Carbon::today()->subDay()->format('Y-m-d H:i:s'), 'end_date' => Carbon::today()->addDay()->format('Y-m-d H:i:s')], $user);

        $this->assertCount(1, $urls);
    }

    public function test_can_filter_urls_by_user_id()
    {
        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('user')->create();

        factory(Url::class)->create(['user_id' => $user->id, 'path' => 'test10001']);
        factory(Url::class, 5)->create(['user_id' => $secondUser->id]);

        $urls = $this->urlService->index(['user_id' => $user->id], $user);

        $this->assertCount(1, $urls);
        $this->assertTrue($urls->first()->path === 'test10001');
    }

    public function test_can_limit_urls()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class, 10)->create(['user_id' => $user->id]);

        $urls = $this->urlService->index(['limit' => 5], $user);

        $this->assertCount(5, $urls);
        $this->assertTrue($urls->total() === 10);
    }

    public function test_can_sort_urls()
    {
        $user = factory(User::class)->states('user')->create();

        factory(Url::class, 10)->create(['user_id' => $user->id]);
        $url = factory(Url::class)->create(['user_id' => $user->id, 'created_at' => Carbon::now()->subDay()]);

        $urls = $this->urlService->index(['sort_by' => 'created_at', 'sort_direction' => 'asc'], $user);

        $this->assertTrue($urls[0]->id === $url->id);
    }

    public function test_cant_shorten_invalid_url()
    {
        $this->expectException(ValidationException::class);

        $longUrl = 'wrong';
        $path = 'test-path';

        $this->urlService->create($longUrl, $path);
    }

    public function test_cant_use_restricted_domain()
    {
        $this->expectException(ValidationException::class);

        $longUrl = 'https://devpri.com';

        $this->urlService->create($longUrl);
    }

    public function test_cant_shorten_with_invalid_path()
    {
        $this->expectException(ValidationException::class);

        $longUrl = 'https://google.com';
        $path = 't';

        $this->urlService->create($longUrl, $path);
    }

    public function test_cant_shorten_with_restricted_path()
    {
        $this->expectException(ValidationException::class);

        $longUrl = 'https://google.com';
        $path = 'dashboard';

        $this->urlService->create($longUrl, $path);
    }

    public function test_cant_shorten_with_duplicate_path()
    {
        $this->expectException(ValidationException::class);

        $longUrl = 'http://google.com';
        $path = 'test-path';

        $this->urlService->create($longUrl, $path);
        $this->urlService->create($longUrl, $path);
    }

    public function test_can_shorten_without_user()
    {
        $longUrl = 'http://google.com';

        $url = $this->urlService->create($longUrl);

        $this->assertTrue($url->long_url === $longUrl);
        $this->assertTrue($url->user_id === null);
    }

    public function test_can_shorten_without_user_with_custom_path()
    {
        $longUrl = 'http://google.com';
        $path = 'test-path';

        $url = $this->urlService->create($longUrl, $path);

        $this->assertTrue($url->long_url === $longUrl);
        $this->assertTrue($url->path === $path);
        $this->assertTrue($url->user_id === null);
    }

    public function test_can_shorten_with_user_with_custom_path()
    {
        $longUrl = 'http://google.com';
        $path = 'test-path';
        $user = factory(User::class)->states('user')->create();

        $url = $this->urlService->create($longUrl, $path, $user);

        $this->assertTrue($url->long_url === $longUrl);
        $this->assertTrue($url->path === $path);
        $this->assertTrue($url->user_id === $user->id);
    }

    public function test_can_update_url()
    {
        $user = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $longUrl = 'http://google.com';
        $path = 'test10234444';

        $updatedUrl = $this->urlService->update($url->id, 0, $longUrl, $path, $user);

        $this->assertTrue($updatedUrl->long_url === $longUrl);
        $this->assertTrue($updatedUrl->path === $path);
    }

    public function test_cant_update_with_invalid_url()
    {
        $this->expectException(ValidationException::class);

        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $longUrl = 'google';

        $this->urlService->update($url->id, $url->active, $longUrl, $url->path, $user);
    }

    public function test_cant_update_with_restricted_url()
    {
        $this->expectException(ValidationException::class);

        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $longUrl = 'devpri.com';

        $this->urlService->update($url->id, $url->active, $longUrl, $url->path, $user);
    }

    public function test_cant_update_with_invalid_path()
    {
        $this->expectException(ValidationException::class);

        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $path = 'g%o&o+gle';

        $this->urlService->update($url->id, $url->active, $url->long_url, $path, $user);
    }

    public function test_cant_update_other_user_url()
    {
        $this->expectException(HttpException::class);

        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('user')->create();

        $url = factory(Url::class)->create(['user_id' => $secondUser->id]);

        $longUrl = 'http://google.com';
        $path = 'test10234444';

        $this->urlService->update($url->id, 0, $longUrl, $path, $user);
    }

    public function test_editor_can_update_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('editor')->create();

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $longUrl = 'http://google.com';
        $path = 'test10234444';

        $updatedUrl = $this->urlService->update($url->id, 0, $longUrl, $path, $secondUser);

        $this->assertTrue($updatedUrl->long_url === $longUrl);
        $this->assertTrue($updatedUrl->path === $path);
    }

    public function test_admin_can_update_other_user_url()
    {
        $user = factory(User::class)->states('user')->create();
        $secondUser = factory(User::class)->states('administrator')->create();

        $url = factory(Url::class)->create(['user_id' => $user->id]);

        $longUrl = 'http://google.com';
        $path = 'test10234444';

        $updatedUrl = $this->urlService->update($url->id, 0, $longUrl, $path, $secondUser);

        $this->assertTrue($updatedUrl->long_url === $longUrl);
        $this->assertTrue($updatedUrl->path === $path);
    }
}
