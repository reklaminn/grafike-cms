<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\SiteSetting;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'settings' => [
                'site_title' => SiteSetting::get('site.title', config('cms.name', 'Grafike CMS')),
                'logo_url' => SiteSetting::get('design.logo_url', ''),
                'favicon_url' => SiteSetting::get('design.favicon_url', ''),
                'footer_text' => SiteSetting::get('site.footer_text', ''),
                'contact' => [
                    'phone' => SiteSetting::get('contact.phone', ''),
                    'email' => SiteSetting::get('contact.email', ''),
                    'address' => SiteSetting::get('contact.address', ''),
                ],
                'social' => [
                    'facebook' => SiteSetting::get('social.facebook', ''),
                    'instagram' => SiteSetting::get('social.instagram', ''),
                    'twitter' => SiteSetting::get('social.twitter', ''),
                    'youtube' => SiteSetting::get('social.youtube', ''),
                    'linkedin' => SiteSetting::get('social.linkedin', ''),
                ],
                'services' => [
                    'google_analytics_id' => SiteSetting::get('services.google_analytics_id', ''),
                    'google_tag_manager_id' => SiteSetting::get('services.google_tag_manager_id', ''),
                    'recaptcha_site_key' => SiteSetting::get('services.recaptcha_site_key', ''),
                ],
            ],
        ]);
    }
}
