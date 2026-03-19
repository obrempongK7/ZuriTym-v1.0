package com.zuritym.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.gms.auth.api.signin.*;
import com.google.android.gms.common.api.ApiException;
import com.google.android.gms.tasks.Task;
import com.zuritym.app.R;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.*;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.HashMap;
import java.util.Map;

public class LoginActivity extends AppCompatActivity {
    private static final int RC_SIGN_IN = 9001;
    private EditText etEmail, etPassword;
    private Button btnLogin;
    private TextView tvRegister;
    private ProgressBar progressBar;
    private GoogleSignInClient googleSignInClient;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        etEmail     = findViewById(R.id.etEmail);
        etPassword  = findViewById(R.id.etPassword);
        btnLogin    = findViewById(R.id.btnLogin);
        tvRegister  = findViewById(R.id.tvRegister);
        progressBar = findViewById(R.id.progressBar);

        GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestIdToken(getString(R.string.default_web_client_id))
                .requestEmail().build();
        googleSignInClient = GoogleSignIn.getClient(this, gso);

        btnLogin.setOnClickListener(v -> doLogin());
        tvRegister.setOnClickListener(v -> startActivity(new Intent(this, RegisterActivity.class)));
        findViewById(R.id.btnGoogle).setOnClickListener(v -> signInWithGoogle());
    }

    private void doLogin() {
        String email = etEmail.getText().toString().trim();
        String pass  = etPassword.getText().toString();
        if (email.isEmpty() || pass.isEmpty()) { AppUtils.showToast(this, "Fill in all fields"); return; }

        setLoading(true);
        Map<String, String> body = new HashMap<>();
        body.put("email", email); body.put("password", pass);
        body.put("device_id", PrefManager.getDeviceId());

        ApiClient.getApiService().login(body).enqueue(new Callback<ApiResponse<AuthData>>() {
            @Override public void onResponse(Call<ApiResponse<AuthData>> call, Response<ApiResponse<AuthData>> response) {
                setLoading(false);
                if (response.isSuccessful() && response.body() != null && response.body().success) {
                    AuthData data = response.body().data;
                    PrefManager.saveToken(data.token);
                    PrefManager.saveUser(data.user);
                    startActivity(new Intent(LoginActivity.this, MainActivity.class));
                    finishAffinity();
                } else {
                    AppUtils.showToast(LoginActivity.this, response.body() != null ? response.body().message : "Login failed");
                }
            }
            @Override public void onFailure(Call<ApiResponse<AuthData>> call, Throwable t) {
                setLoading(false); AppUtils.showToast(LoginActivity.this, "Network error. Try again.");
            }
        });
    }

    private void signInWithGoogle() {
        startActivityForResult(googleSignInClient.getSignInIntent(), RC_SIGN_IN);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == RC_SIGN_IN) {
            Task<GoogleSignInAccount> task = GoogleSignIn.getSignedInAccountFromIntent(data);
            try {
                GoogleSignInAccount account = task.getResult(ApiException.class);
                String idToken = account.getIdToken();
                setLoading(true);
                Map<String, String> body = new HashMap<>();
                body.put("id_token", idToken);
                body.put("device_id", PrefManager.getDeviceId());
                ApiClient.getApiService().googleAuth(body).enqueue(new Callback<ApiResponse<AuthData>>() {
                    @Override public void onResponse(Call<ApiResponse<AuthData>> call, Response<ApiResponse<AuthData>> r) {
                        setLoading(false);
                        if (r.isSuccessful() && r.body() != null && r.body().success) {
                            PrefManager.saveToken(r.body().data.token);
                            PrefManager.saveUser(r.body().data.user);
                            startActivity(new Intent(LoginActivity.this, MainActivity.class));
                            finishAffinity();
                        } else AppUtils.showToast(LoginActivity.this, "Google sign-in failed");
                    }
                    @Override public void onFailure(Call<ApiResponse<AuthData>> call, Throwable t) { setLoading(false); }
                });
            } catch (ApiException e) { AppUtils.showToast(this, "Google sign-in error"); }
        }
    }

    private void setLoading(boolean loading) {
        progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        btnLogin.setEnabled(!loading);
    }
}
