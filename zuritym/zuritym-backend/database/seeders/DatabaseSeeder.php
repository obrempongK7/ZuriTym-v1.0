<?php

namespace Database\Seeders;

use App\Models\AdNetwork;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\SpinReward;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(['email' => 'admin@zuritym.com'], [
            'name'     => 'ZuriTym Admin',
            'email'    => 'admin@zuritym.com',
            'password' => 'Admin@123456',
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        // Default settings
        $settings = [
            ['key'=>'app_name',          'value'=>'ZuriTym',         'group'=>'general'],
            ['key'=>'app_version',       'value'=>'1.0.0',           'group'=>'general'],
            ['key'=>'min_withdrawal',    'value'=>'500',             'group'=>'wallet'],
            ['key'=>'referral_reward',   'value'=>'50',              'group'=>'wallet'],
            ['key'=>'signup_bonus',      'value'=>'10',              'group'=>'wallet'],
            ['key'=>'spin_daily_limit',  'value'=>'3',               'group'=>'spin'],
            ['key'=>'scratch_daily_limit','value'=>'5',              'group'=>'scratch'],
            ['key'=>'maintenance_mode',  'value'=>'false',           'group'=>'general'],
            ['key'=>'announcement_text', 'value'=>'Welcome to ZuriTym! Complete tasks and earn rewards daily.', 'group'=>'general'],
            ['key'=>'contact_email',     'value'=>'support@zuritym.com', 'group'=>'general'],
            ['key'=>'privacy_url',       'value'=>'https://zuritym.com/privacy', 'group'=>'general'],
            ['key'=>'terms_url',         'value'=>'https://zuritym.com/terms',   'group'=>'general'],
        ];
        foreach ($settings as $s) Setting::updateOrCreate(['key'=>$s['key']], $s);

        // Spin wheel segments
        $spinSegments = [
            ['label'=>'5 Pts',   'points'=>5,   'type'=>'points', 'probability'=>30, 'color'=>'#FF6B6B', 'sort_order'=>1],
            ['label'=>'10 Pts',  'points'=>10,  'type'=>'points', 'probability'=>25, 'color'=>'#4ECDC4', 'sort_order'=>2],
            ['label'=>'20 Pts',  'points'=>20,  'type'=>'points', 'probability'=>18, 'color'=>'#45B7D1', 'sort_order'=>3],
            ['label'=>'50 Pts',  'points'=>50,  'type'=>'points', 'probability'=>12, 'color'=>'#96CEB4', 'sort_order'=>4],
            ['label'=>'100 Pts', 'points'=>100, 'type'=>'points', 'probability'=>8,  'color'=>'#FFEAA7', 'sort_order'=>5],
            ['label'=>'Try Again','points'=>0,  'type'=>'empty',  'probability'=>5,  'color'=>'#DDD',    'sort_order'=>6],
            ['label'=>'200 Pts', 'points'=>200, 'type'=>'points', 'probability'=>2,  'color'=>'#FD79A8', 'sort_order'=>7],
        ];
        foreach ($spinSegments as $seg) SpinReward::updateOrCreate(['label'=>$seg['label']], $seg + ['is_active'=>true]);

        // Sample tasks
        $tasks = [
            ['title'=>'Watch a Video','type'=>'watch_video','reward_points'=>5,'timer_seconds'=>30,'daily_limit'=>5,'description'=>'Watch the full video to earn points.','sort_order'=>1],
            ['title'=>'Visit Website','type'=>'visit_website','reward_points'=>3,'timer_seconds'=>15,'daily_limit'=>10,'action_url'=>'https://example.com','description'=>'Visit the website and stay for 15 seconds.','sort_order'=>2],
            ['title'=>'Daily Check-in','type'=>'daily_offer','reward_points'=>10,'timer_seconds'=>0,'daily_limit'=>1,'description'=>'Claim your daily bonus!','sort_order'=>3],
            ['title'=>'Math Quiz','type'=>'quiz','reward_points'=>15,'timer_seconds'=>0,'daily_limit'=>3,'description'=>'Complete the math quiz to earn points.','sort_order'=>4],
        ];
        foreach ($tasks as $t) Task::updateOrCreate(['title'=>$t['title']], $t + ['is_active'=>true]);

        // Payment methods
        $methods = [
            ['name'=>'M-Pesa', 'slug'=>'mpesa','min_withdrawal'=>500,'conversion_rate'=>0.001,'fields'=>[['name'=>'phone','label'=>'M-Pesa Phone Number','type'=>'text','required'=>true]]],
            ['name'=>'PayPal', 'slug'=>'paypal','min_withdrawal'=>1000,'conversion_rate'=>0.001,'fields'=>[['name'=>'email','label'=>'PayPal Email','type'=>'email','required'=>true]]],
            ['name'=>'Bank Transfer','slug'=>'bank','min_withdrawal'=>2000,'conversion_rate'=>0.001,'fields'=>[['name'=>'account','label'=>'Account Number','type'=>'text','required'=>true],['name'=>'bank','label'=>'Bank Name','type'=>'text','required'=>true]]],
        ];
        foreach ($methods as $m) PaymentMethod::updateOrCreate(['slug'=>$m['slug']], $m + ['is_active'=>true,'sort_order'=>1,'fields'=>$m['fields']]);

        // Ad networks
        $adNetworks = [
            ['name'=>'Google AdMob',   'slug'=>'admob',   'config'=>['app_id'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
            ['name'=>'Facebook Ads',   'slug'=>'facebook', 'config'=>['placement_id'=>'','banner_id'=>'','interstitial_id'=>'']],
            ['name'=>'AppLovin',       'slug'=>'applovin', 'config'=>['sdk_key'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
            ['name'=>'Unity Ads',      'slug'=>'unity',    'config'=>['game_id'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
            ['name'=>'IronSource',     'slug'=>'ironsource','config'=>['app_key'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
            ['name'=>'Wortise',        'slug'=>'wortise',  'config'=>['app_id'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
            ['name'=>'Vungle',         'slug'=>'vungle',   'config'=>['app_id'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
            ['name'=>'AppLovin MAX',   'slug'=>'max',      'config'=>['sdk_key'=>'','banner_id'=>'','interstitial_id'=>'','rewarded_id'=>'']],
        ];
        foreach ($adNetworks as $n) AdNetwork::updateOrCreate(['slug'=>$n['slug']], $n + ['is_active'=>false,'sort_order'=>1]);

        $this->command->info('ZuriTym seeded successfully! Admin: admin@zuritym.com / Admin@123456');
    }
}
