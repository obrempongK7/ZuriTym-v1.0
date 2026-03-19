<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\AdNetwork;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller {
    public function index(Request $request): JsonResponse {
        $user  = $request->user()->load('wallet','leaderboard');
        $tasks = Task::where('is_active',true)->orderBy('sort_order')->take(6)->get()
                     ->map(fn($t)=>['id'=>$t->id,'title'=>$t->title,'type'=>$t->type,'reward_points'=>$t->reward_points,'icon_url'=>$t->icon?asset('storage/tasks/'.$t->icon):null]);
        return response()->json(['success'=>true,'data'=>[
            'user'=>['name'=>$user->name,'balance'=>$user->wallet?->balance??0,'rank'=>$user->leaderboard?->rank??0,'avatar_url'=>$user->avatar_url],
            'featured_tasks'=>$tasks,
            'banner'=>Setting::get('home_banner_url'),
            'announcement'=>Setting::get('announcement_text'),
            'spin_available'=>$user->today_spin_count < (int)Setting::get('spin_daily_limit',3),
            'scratch_available'=>$user->today_scratch_count < (int)Setting::get('scratch_daily_limit',5),
        ]]);
    }
    public function appSettings(): JsonResponse {
        $adNetworks = AdNetwork::where('is_active',true)->get()->mapWithKeys(fn($n)=>[$n->slug=>$n->config]);
        return response()->json(['success'=>true,'data'=>[
            'app_name'=>Setting::get('app_name','ZuriTym'),
            'min_withdrawal'=>Setting::get('min_withdrawal',500),
            'referral_reward'=>Setting::get('referral_reward',50),
            'spin_daily_limit'=>Setting::get('spin_daily_limit',3),
            'scratch_daily_limit'=>Setting::get('scratch_daily_limit',5),
            'privacy_url'=>Setting::get('privacy_url'),
            'terms_url'=>Setting::get('terms_url'),
            'contact_email'=>Setting::get('contact_email'),
            'ad_networks'=>$adNetworks,
            'maintenance_mode'=>Setting::get('maintenance_mode','false'),
        ]]);
    }
}
