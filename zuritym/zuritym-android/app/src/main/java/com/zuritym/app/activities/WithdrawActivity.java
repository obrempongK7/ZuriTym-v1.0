package com.zuritym.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import com.zuritym.app.R;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.AppUtils;
import com.zuritym.app.utils.PrefManager;
import okhttp3.RequestBody;
import okhttp3.MediaType;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.List;

public class WithdrawActivity extends AppCompatActivity {
    private EditText etAmount, etPaymentDetail;
    private Spinner spMethod;
    private Button btnWithdraw;
    private TextView tvBalance, tvMinWithdrawal;
    private ProgressBar progressBar;
    private List<PaymentMethod> methods;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_withdraw);
        etAmount        = findViewById(R.id.etAmount);
        etPaymentDetail = findViewById(R.id.etPaymentDetail);
        spMethod        = findViewById(R.id.spMethod);
        btnWithdraw     = findViewById(R.id.btnWithdraw);
        tvBalance       = findViewById(R.id.tvBalance);
        tvMinWithdrawal = findViewById(R.id.tvMinWithdrawal);
        progressBar     = findViewById(R.id.progressBar);
        tvBalance.setText("Balance: " + AppUtils.formatPoints(PrefManager.getBalance()));
        btnWithdraw.setOnClickListener(v -> submitWithdrawal());
        loadPaymentMethods();
    }

    private void loadPaymentMethods() {
        ApiClient.getApiService().getPaymentMethods().enqueue(new Callback<ApiResponse<List<PaymentMethod>>>() {
            @Override public void onResponse(Call<ApiResponse<List<PaymentMethod>>> c, Response<ApiResponse<List<PaymentMethod>>> r) {
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    methods = r.body().data;
                    String[] names = new String[methods.size()];
                    for (int i = 0; i < methods.size(); i++) names[i] = methods.get(i).name;
                    spMethod.setAdapter(new ArrayAdapter<>(WithdrawActivity.this, android.R.layout.simple_spinner_item, names));
                    if (!methods.isEmpty()) tvMinWithdrawal.setText("Min: " + AppUtils.formatPoints(methods.get(0).minWithdrawal));
                    spMethod.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                        @Override public void onItemSelected(AdapterView<?> p, View v, int pos, long id) {
                            tvMinWithdrawal.setText("Min: " + AppUtils.formatPoints(methods.get(pos).minWithdrawal));
                        }
                        @Override public void onNothingSelected(AdapterView<?> p) {}
                    });
                }
            }
            @Override public void onFailure(Call<ApiResponse<List<PaymentMethod>>> c, Throwable t) {}
        });
    }

    private void submitWithdrawal() {
        String amount = etAmount.getText().toString().trim();
        String detail = etPaymentDetail.getText().toString().trim();
        if (amount.isEmpty() || detail.isEmpty()) { AppUtils.showToast(this,"Fill all fields"); return; }
        if (methods == null || methods.isEmpty()) { AppUtils.showToast(this,"Loading payment methods..."); return; }
        int idx = spMethod.getSelectedItemPosition();
        PaymentMethod method = methods.get(idx);
        progressBar.setVisibility(View.VISIBLE);
        btnWithdraw.setEnabled(false);
        RequestBody amtBody    = RequestBody.create(amount, MediaType.parse("text/plain"));
        RequestBody methodBody = RequestBody.create(method.slug, MediaType.parse("text/plain"));
        RequestBody detailBody = RequestBody.create(detail, MediaType.parse("text/plain"));
        ApiClient.getApiService().requestWithdrawal(amtBody, methodBody, detailBody, null).enqueue(new Callback<ApiResponse<Object>>() {
            @Override public void onResponse(Call<ApiResponse<Object>> c, Response<ApiResponse<Object>> r) {
                progressBar.setVisibility(View.GONE);
                btnWithdraw.setEnabled(true);
                AppUtils.showToast(WithdrawActivity.this, r.body() != null ? r.body().message : "Error");
                if (r.isSuccessful() && r.body() != null && r.body().success) finish();
            }
            @Override public void onFailure(Call<ApiResponse<Object>> c, Throwable t) { progressBar.setVisibility(View.GONE); btnWithdraw.setEnabled(true); }
        });
    }
}
