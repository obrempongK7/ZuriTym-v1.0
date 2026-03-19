package com.zuritym.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import com.zuritym.app.R;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.AppUtils;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ScratchCardActivity extends AppCompatActivity {
    private TextView tvBalance, tvResult, tvCardsLeft;
    private Button btnGetCard, btnScratch;
    private ProgressBar progressBar;
    private int currentCardId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scratch_card);
        tvBalance   = findViewById(R.id.tvBalance);
        tvResult    = findViewById(R.id.tvResult);
        tvCardsLeft = findViewById(R.id.tvCardsLeft);
        btnGetCard  = findViewById(R.id.btnGetCard);
        btnScratch  = findViewById(R.id.btnScratch);
        progressBar = findViewById(R.id.progressBar);
        btnGetCard.setOnClickListener(v -> issueCard());
        btnScratch.setOnClickListener(v -> { if (currentCardId > 0) doScratch(); });
        btnScratch.setEnabled(false);
    }

    private void issueCard() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().issueScratchCard().enqueue(new Callback<ApiResponse<ScratchCardData>>() {
            @Override public void onResponse(Call<ApiResponse<ScratchCardData>> c, Response<ApiResponse<ScratchCardData>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    currentCardId = r.body().data.cardId;
                    tvCardsLeft.setText("Cards left: " + r.body().data.cardsRemaining);
                    btnScratch.setEnabled(true);
                    tvResult.setText("Card ready! Tap Scratch to reveal.");
                } else AppUtils.showToast(ScratchCardActivity.this, r.body() != null ? r.body().message : "Error");
            }
            @Override public void onFailure(Call<ApiResponse<ScratchCardData>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }

    private void doScratch() {
        progressBar.setVisibility(View.VISIBLE);
        btnScratch.setEnabled(false);
        ApiClient.getApiService().scratchCard(currentCardId).enqueue(new Callback<ApiResponse<ScratchResult>>() {
            @Override public void onResponse(Call<ApiResponse<ScratchResult>> c, Response<ApiResponse<ScratchResult>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    ScratchResult res = r.body().data;
                    tvResult.setText(res.pointsWon > 0 ? "You won " + (int)res.pointsWon + " pts!" : "Better luck next time!");
                    tvBalance.setText(AppUtils.formatPoints(res.newBalance));
                    currentCardId = -1;
                } else AppUtils.showToast(ScratchCardActivity.this, "Error scratching card");
            }
            @Override public void onFailure(Call<ApiResponse<ScratchResult>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }
}
