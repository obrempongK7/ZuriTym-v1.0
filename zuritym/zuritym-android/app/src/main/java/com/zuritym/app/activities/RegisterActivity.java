package com.zuritym.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import com.zuritym.app.R;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.*;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.HashMap;
import java.util.Map;

public class RegisterActivity extends AppCompatActivity {
    private EditText etName, etEmail, etPassword, etConfirm, etReferral;
    private Button btnRegister;
    private ProgressBar progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);
        etName     = findViewById(R.id.etName);
        etEmail    = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        etConfirm  = findViewById(R.id.etConfirm);
        etReferral = findViewById(R.id.etReferral);
        btnRegister = findViewById(R.id.btnRegister);
        progressBar = findViewById(R.id.progressBar);
        btnRegister.setOnClickListener(v -> doRegister());
        findViewById(R.id.tvLogin).setOnClickListener(v -> finish());
    }

    private void doRegister() {
        String name  = etName.getText().toString().trim();
        String email = etEmail.getText().toString().trim();
        String pass  = etPassword.getText().toString();
        String conf  = etConfirm.getText().toString();
        if (name.isEmpty() || email.isEmpty() || pass.isEmpty()) { AppUtils.showToast(this,"Fill all fields"); return; }
        if (!pass.equals(conf)) { AppUtils.showToast(this,"Passwords don't match"); return; }

        setLoading(true);
        Map<String, String> body = new HashMap<>();
        body.put("name", name); body.put("email", email);
        body.put("password", pass); body.put("password_confirmation", conf);
        body.put("device_id", PrefManager.getDeviceId());
        String ref = etReferral.getText().toString().trim();
        if (!ref.isEmpty()) body.put("referral_code", ref);

        ApiClient.getApiService().register(body).enqueue(new Callback<ApiResponse<AuthData>>() {
            @Override public void onResponse(Call<ApiResponse<AuthData>> call, Response<ApiResponse<AuthData>> r) {
                setLoading(false);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    PrefManager.saveToken(r.body().data.token);
                    PrefManager.saveUser(r.body().data.user);
                    startActivity(new Intent(RegisterActivity.this, MainActivity.class));
                    finishAffinity();
                } else AppUtils.showToast(RegisterActivity.this, r.body() != null ? r.body().message : "Registration failed");
            }
            @Override public void onFailure(Call<ApiResponse<AuthData>> call, Throwable t) {
                setLoading(false); AppUtils.showToast(RegisterActivity.this, "Network error");
            }
        });
    }

    private void setLoading(boolean l) { progressBar.setVisibility(l ? View.VISIBLE : View.GONE); btnRegister.setEnabled(!l); }
}
