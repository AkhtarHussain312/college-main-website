<?php

namespace App\Providers;

use App\Models\SiteContent;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Ensure physical public/favicon.ico mirrors public/logo.png
        $logo = public_path('logo.png');
        $ico = public_path('favicon.ico');
        if (file_exists($logo)) {
            if (!file_exists($ico) || @filesize($ico) !== @filesize($logo)) {
                @copy($logo, $ico);
            }
        }

        // 2. Runtime synchronization for college branding
        try {
            if (Schema::hasTable('site_contents')) {
                SiteContent::where('key', 'college_name')
                    ->where('value', 'Pakistan Leadership College')
                    ->update(['value' => 'Dir College Of Nursing & Allied Health Science']);

                SiteContent::where('key', 'college_short_name')
                    ->where('value', 'PLC')
                    ->update(['value' => 'DCN']);

                SiteContent::where('key', 'nav_brand_title')
                    ->where('value', 'PLC')
                    ->update(['value' => 'DCN']);

                SiteContent::where('key', 'nav_brand_subtitle')
                    ->where('value', 'Pakistan Leadership College')
                    ->update(['value' => 'Dir College Of Nursing & Allied Health Science']);

                SiteContent::where('key', 'hero_title')
                    ->where('value', 'Pakistan Leadership College')
                    ->update(['value' => 'Dir College Of Nursing & Allied Health Science']);

                SiteContent::where('key', 'metric_3_value')
                    ->where('value', 'PLC')
                    ->update(['value' => 'DCN']);
            }
        } catch (\Throwable) {
        }
    }
}
