<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller {
    public function index() { $codes = PromoCode::latest()->paginate(20); return view('admin.promo.index',compact('codes')); }
    public function create() { return view('admin.promo.form',['code'=>new PromoCode()]); }
    public function store(Request $request) {
        $request->validate(['code'=>'required|unique:promo_codes,code','reward_points'=>'required|numeric','expires_at'=>'nullable|date']);
        $data = $request->all();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);
        PromoCode::create($data);
        return redirect()->route('admin.promo-codes.index')->with('success','Promo code created!');
    }
    public function edit(PromoCode $promoCode) { return view('admin.promo.form',['code'=>$promoCode]); }
    public function update(Request $request, PromoCode $promoCode) {
        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');
        $promoCode->update($data);
        return redirect()->route('admin.promo-codes.index')->with('success','Updated!');
    }
    public function destroy(PromoCode $promoCode) { $promoCode->delete(); return back()->with('success','Deleted.'); }
}
