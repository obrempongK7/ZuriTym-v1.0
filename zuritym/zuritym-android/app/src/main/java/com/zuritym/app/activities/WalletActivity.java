package com.zuritym.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.adapters.TransactionAdapter;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.AppUtils;
import com.zuritym.app.utils.PrefManager;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class WalletActivity extends AppCompatActivity {
    private TextView tvBalance, tvTotalEarned, tvTotalWithdrawn;
    private RecyclerView rvTransactions;
    private TransactionAdapter adapter;
    private ProgressBar progressBar;
    private EditText etPromoCode;
    private Button btnRedeem;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_wallet);
        tvBalance       = findViewById(R.id.tvBalance);
        tvTotalEarned   = findViewById(R.id.tvTotalEarned);
        tvTotalWithdrawn = findViewById(R.id.tvTotalWithdrawn);
        rvTransactions  = findViewById(R.id.rvTransactions);
        progressBar     = findViewById(R.id.progressBar);
        etPromoCode     = findViewById(R.id.etPromoCode);
        btnRedeem       = findViewById(R.id.btnRedeem);
        rvTransactions.setLayoutManager(new LinearLayoutManager(this));
        adapter = new TransactionAdapter();
        rvTransactions.setAdapter(adapter);
        btnRedeem.setOnClickListener(v -> redeemPromo());
        findViewById(R.id.btnWithdraw).setOnClickListener(v ->
                startActivity(new android.content.Intent(this, WithdrawActivity.class)));
        loadWallet();
        loadTransactions();
    }

    private void loadWallet() {
        ApiClient.getApiService().getWalletBalance().enqueue(new Callback<ApiResponse<WalletData>>() {
            @Override public void onResponse(Call<ApiResponse<WalletData>> c, Response<ApiResponse<WalletData>> r) {
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    WalletData w = r.body().data;
                    tvBalance.setText(AppUtils.formatPoints(w.balance));
                    tvTotalEarned.setText(AppUtils.formatPoints(w.totalEarned));
                    tvTotalWithdrawn.setText(AppUtils.formatPoints(w.totalWithdrawn));
                }
            }
            @Override public void onFailure(Call<ApiResponse<WalletData>> c, Throwable t) {}
        });
    }

    private void loadTransactions() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().getTransactions(1, null).enqueue(new Callback<ApiResponse<TransactionListData>>() {
            @Override public void onResponse(Call<ApiResponse<TransactionListData>> c, Response<ApiResponse<TransactionListData>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    adapter.setData(r.body().data.transactions);
                }
            }
            @Override public void onFailure(Call<ApiResponse<TransactionListData>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }

    private void redeemPromo() {
        String code = etPromoCode.getText().toString().trim().toUpperCase();
        if (code.isEmpty()) { AppUtils.showToast(this, "Enter promo code"); return; }
        java.util.Map<String, String> body = new java.util.HashMap<>();
        body.put("code", code);
        ApiClient.getApiService().redeemPromoCode(body).enqueue(new Callback<ApiResponse<PromoResult>>() {
            @Override public void onResponse(Call<ApiResponse<PromoResult>> c, Response<ApiResponse<PromoResult>> r) {
                AppUtils.showToast(WalletActivity.this, r.body() != null ? r.body().message : "Error");
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    etPromoCode.setText("");
                    loadWallet();
                    loadTransactions();
                }
            }
            @Override public void onFailure(Call<ApiResponse<PromoResult>> c, Throwable t) {}
        });
    }
}
