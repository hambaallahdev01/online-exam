<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocaleFromIp;
use App\Http\Middleware\SetTenantLocale;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class MultiLanguageTenantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all 4 language translation files have matching keys.
     */
    public function test_all_language_files_exist_and_have_keys(): void
    {
        $locales = ['id', 'en', 'ar', 'zh'];

        foreach ($locales as $locale) {
            $filePath = base_path("lang/{$locale}/messages.php");
            $this->assertFileExists($filePath, "Lang file for {$locale} must exist.");

            $translations = include $filePath;
            $this->assertIsArray($translations);
            $this->assertArrayHasKey('brand_name', $translations);
            $this->assertArrayHasKey('hero_title', $translations);
            $this->assertArrayHasKey('school_identity', $translations);
            $this->assertArrayHasKey('language_setting', $translations);
        }
    }

    /**
     * Test tenant dashboard applies the school's configured locale.
     */
    public function test_tenant_locale_is_applied_for_authenticated_users(): void
    {
        $schoolAr = School::create([
            'name' => 'Al-Azhar Academy',
            'code' => 'AZHAR01',
            'email' => 'admin@azhar.edu',
            'locale' => 'ar',
        ]);

        $userAr = User::create([
            'school_id' => $schoolAr->id,
            'name' => 'Ahmad Teacher',
            'email' => 'ahmad@azhar.edu',
            'role' => 'teacher',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($userAr);

        $request = Request::create('/teacher/dashboard', 'GET');
        $request->setUserResolver(fn () => $userAr);

        $middleware = new SetTenantLocale();
        $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertEquals('ar', App::getLocale(), 'Locale should be set to Arabic for school with locale=ar');
    }

    /**
     * Test tenant dashboard applies Chinese locale when configured.
     */
    public function test_tenant_locale_chinese(): void
    {
        $schoolZh = School::create([
            'name' => 'Beijing High School',
            'code' => 'BJ01',
            'email' => 'admin@beijing.edu',
            'locale' => 'zh',
        ]);

        $userZh = User::create([
            'school_id' => $schoolZh->id,
            'name' => 'Li Wei Student',
            'email' => 'liwei@beijing.edu',
            'role' => 'student',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($userZh);

        $request = Request::create('/student/dashboard', 'GET');
        $request->setUserResolver(fn () => $userZh);

        $middleware = new SetTenantLocale();
        $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertEquals('zh', App::getLocale(), 'Locale should be set to Chinese for school with locale=zh');
    }

    /**
     * Test IP-based locale detection middleware with mock IPs.
     */
    public function test_guest_landing_page_defaults_to_indonesian(): void
    {
        Session::flush();

        $request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
        ]);

        $middleware = new SetLocaleFromIp();
        $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertEquals('id', App::getLocale(), 'Default locale for localhost/fallback should be Indonesian (id)');
    }

    /**
     * Test admin can update school profile language.
     */
    public function test_admin_can_update_school_language(): void
    {
        $school = School::create([
            'name' => 'Global School',
            'code' => 'GS01',
            'email' => 'admin@globalschool.com',
            'locale' => 'id',
        ]);

        $admin = User::create([
            'school_id' => $school->id,
            'name' => 'Admin User',
            'email' => 'admin@globalschool.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin)->post('/admin/profile', [
            'name' => 'Global School Updated',
            'email' => 'admin@globalschool.com',
            'locale' => 'en',
        ]);

        $response->assertSessionHasNoErrors();
        $school->refresh();
        $this->assertEquals('en', $school->locale);
    }
}
