<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function __construct(protected PushNotificationService $pushService) {}
    public function index() { $notifs = Notification::latest()->paginate(20); return view('admin.notifications.index',compact('notifs')); }
    public function create() { return view('admin.notifications.create'); }
    public function send(Request $request) {
        $request->validate(['title'=>'required|string','body'=>'required|string','target'=>'required|in:all,specific']);
        $notif = Notification::create([...$request->only(['title','body','target','data']),'created_by'=>auth()->id(),'status'=>'sent']);
        try {
            $tokens = $request->target === 'all'
                ? User::whereNotNull('fcm_token')->active()->pluck('fcm_token')->toArray()
                : User::whereIn('id', explode(',', $request->user_ids ?? ''))->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
            $this->pushService->sendToTokens($tokens, $request->title, $request->body, $request->data ?? []);
            $notif->update(['sent_count'=>count($tokens)]);
        } catch(\Exception $e) { $notif->update(['status'=>'failed']); }
        return redirect()->route('admin.notifications.index')->with('success','Notification sent!');
    }
}
