<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SpinReward;
use Illuminate\Http\Request;

class SpinAdminController extends Controller {
    public function index() { $rewards = SpinReward::orderBy('sort_order')->get(); return view('admin.spin.index',compact('rewards')); }
    public function create() { return view('admin.spin.form',['reward'=>new SpinReward()]); }
    public function store(Request $request) {
        $request->validate(['label'=>'required','points'=>'required|numeric','probability'=>'required|integer|min:1|max:100','color'=>'required']);
        SpinReward::create($request->merge(['is_active'=>$request->boolean('is_active')])->all());
        return redirect()->route('admin.spin-rewards.index')->with('success','Segment added!');
    }
    public function edit(SpinReward $spinReward) { return view('admin.spin.form',['reward'=>$spinReward]); }
    public function update(Request $request, SpinReward $spinReward) {
        $spinReward->update($request->merge(['is_active'=>$request->boolean('is_active')])->all());
        return redirect()->route('admin.spin-rewards.index')->with('success','Segment updated!');
    }
    public function destroy(SpinReward $spinReward) { $spinReward->delete(); return back()->with('success','Deleted.'); }
}
