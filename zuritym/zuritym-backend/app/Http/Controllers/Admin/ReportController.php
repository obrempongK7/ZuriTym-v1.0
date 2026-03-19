<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function transactions(Request $request) {
        $query = Transaction::with('user')->latest();
        if ($request->from) $query->whereDate('created_at','>=',$request->from);
        if ($request->to)   $query->whereDate('created_at','<=',$request->to);
        if ($request->type) $query->where('type',$request->type);
        $transactions = $query->paginate(25)->withQueryString();
        return view('admin.reports.transactions', compact('transactions'));
    }
    public function export(Request $request) {
        $txns = Transaction::with('user')->latest()->get();
        $csv = "ID,User,Email,Amount,Type,Status,Date\n";
        foreach ($txns as $t) {
            $csv .= "{$t->txn_id},{$t->user?->name},{$t->user?->email},{$t->amount},{$t->type},{$t->status},{$t->created_at}\n";
        }
        return response($csv,200,['Content-Type'=>'text/csv','Content-Disposition'=>'attachment;filename=transactions.csv']);
    }
}
