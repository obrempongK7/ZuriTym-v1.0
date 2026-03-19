<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdNetwork;
use Illuminate\Http\Request;

class AdNetworkController extends Controller {
    public function index() { $networks = AdNetwork::orderBy('sort_order')->get(); return view('admin.settings.ad-networks', compact('networks')); }
    public function update(Request $request) {
        foreach ($request->networks ?? [] as $id => $data) {
            AdNetwork::find($id)?->update(['config'=>$data['config'] ?? [],'is_active'=>isset($data['is_active'])]);
        }
        return back()->with('success', 'Ad network settings saved!');
    }
}
