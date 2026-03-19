<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller {
    public function index(): JsonResponse {
        $messages = ChatMessage::with('user:id,name,avatar')->where('is_deleted',false)->latest()->take(50)->get()->reverse()->values()
            ->map(fn($m)=>['id'=>$m->id,'message'=>$m->message,'user'=>['id'=>$m->user?->id,'name'=>$m->user?->name,'avatar_url'=>$m->user?->avatar_url],'time'=>$m->created_at->diffForHumans()]);
        return response()->json(['success'=>true,'data'=>$messages]);
    }
    public function send(Request $request): JsonResponse {
        $request->validate(['message'=>'required|string|max:500']);
        $msg = ChatMessage::create(['user_id'=>$request->user()->id,'message'=>$request->message]);
        $msg->load('user:id,name,avatar');
        return response()->json(['success'=>true,'data'=>['id'=>$msg->id,'message'=>$msg->message,'user'=>['id'=>$msg->user?->id,'name'=>$msg->user?->name,'avatar_url'=>$msg->user?->avatar_url],'time'=>'just now']]);
    }
}
