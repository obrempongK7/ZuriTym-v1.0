<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Offerwall;
use Illuminate\Http\Request;

class OfferwallAdminController extends Controller {
    public function index() { $walls = Offerwall::orderBy('sort_order')->paginate(20); return view('admin.offerwalls.index',compact('walls')); }
    public function create() { return view('admin.offerwalls.form',['wall'=>new Offerwall()]); }
    public function store(Request $request) {
        $request->validate(['name'=>'required','slug'=>'required|unique:offerwalls,slug','type'=>'required|in:api,web,sdk']);
        $data = $request->except(['_token','icon']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('icon')) $data['icon'] = $request->file('icon')->store('offerwalls','public');
        Offerwall::create($data);
        return redirect()->route('admin.offerwalls.index')->with('success','Offerwall added!');
    }
    public function edit(Offerwall $offerwall) { return view('admin.offerwalls.form',['wall'=>$offerwall]); }
    public function update(Request $request, Offerwall $offerwall) {
        $data = $request->except(['_token','_method','icon']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('icon')) $data['icon'] = $request->file('icon')->store('offerwalls','public');
        $offerwall->update($data);
        return redirect()->route('admin.offerwalls.index')->with('success','Updated!');
    }
    public function destroy(Offerwall $offerwall) { $offerwall->delete(); return back()->with('success','Deleted.'); }
}
