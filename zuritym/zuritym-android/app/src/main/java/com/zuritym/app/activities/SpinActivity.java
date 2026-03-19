package com.zuritym.app.activities;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ObjectAnimator;
import android.os.Bundle;
import android.view.View;
import android.view.animation.DecelerateInterpolator;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import com.zuritym.app.R;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.AppUtils;
import com.zuritym.app.utils.PrefManager;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.List;

public class SpinActivity extends AppCompatActivity {
    private View spinWheel;
    private Button btnSpin;
    private TextView tvBalance, tvResult, tvSpinsLeft;
    private ProgressBar progressBar;
    private List<SpinConfig.SpinSegment> segments;
    private int spinsRemaining = 0;
    private boolean isSpinning = false;
    private float currentRotation = 0f;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_spin);
        spinWheel   = findViewById(R.id.spinWheel);
        btnSpin     = findViewById(R.id.btnSpin);
        tvBalance   = findViewById(R.id.tvBalance);
        tvResult    = findViewById(R.id.tvResult);
        tvSpinsLeft = findViewById(R.id.tvSpinsLeft);
        progressBar = findViewById(R.id.progressBar);
        tvBalance.setText(AppUtils.formatPoints(PrefManager.getBalance()));
        btnSpin.setOnClickListener(v -> { if (!isSpinning) performSpin(); });
        loadSpinConfig();
    }

    private void loadSpinConfig() {
        ApiClient.getApiService().getSpinConfig().enqueue(new Callback<ApiResponse<SpinConfig>>() {
            @Override public void onResponse(Call<ApiResponse<SpinConfig>> call, Response<ApiResponse<SpinConfig>> r) {
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    segments = r.body().data.segments;
                    spinsRemaining = r.body().data.dailyLimit;
                    tvSpinsLeft.setText("Spins left today: " + spinsRemaining);
                }
            }
            @Override public void onFailure(Call<ApiResponse<SpinConfig>> call, Throwable t) {}
        });
    }

    private void performSpin() {
        isSpinning = true;
        btnSpin.setEnabled(false);
        progressBar.setVisibility(View.VISIBLE);

        ApiClient.getApiService().spin().enqueue(new Callback<ApiResponse<SpinResult>>() {
            @Override public void onResponse(Call<ApiResponse<SpinResult>> call, Response<ApiResponse<SpinResult>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    SpinResult result = r.body().data;
                    animateSpin(result);
                } else {
                    isSpinning = false;
                    btnSpin.setEnabled(true);
                    AppUtils.showToast(SpinActivity.this, r.body() != null ? r.body().message : "Spin failed");
                }
            }
            @Override public void onFailure(Call<ApiResponse<SpinResult>> call, Throwable t) {
                progressBar.setVisibility(View.GONE);
                isSpinning = false;
                btnSpin.setEnabled(true);
            }
        });
    }

    private void animateSpin(SpinResult result) {
        float targetRotation = currentRotation + 1440f + (float)(Math.random() * 360);
        ObjectAnimator anim = ObjectAnimator.ofFloat(spinWheel, "rotation", currentRotation, targetRotation);
        anim.setDuration(3000);
        anim.setInterpolator(new DecelerateInterpolator());
        anim.addListener(new AnimatorListenerAdapter() {
            @Override public void onAnimationEnd(Animator animation) {
                currentRotation = targetRotation % 360;
                isSpinning = false;
                btnSpin.setEnabled(spinsRemaining > 0);
                spinsRemaining = result.spinsRemaining;
                tvSpinsLeft.setText("Spins left today: " + spinsRemaining);
                tvBalance.setText(AppUtils.formatPoints(result.newBalance));
                tvResult.setVisibility(View.VISIBLE);
                tvResult.setText(result.pointsWon > 0
                    ? "You won " + (int)result.pointsWon + " pts!"
                    : "Try again tomorrow!");
            }
        });
        anim.start();
    }
}
