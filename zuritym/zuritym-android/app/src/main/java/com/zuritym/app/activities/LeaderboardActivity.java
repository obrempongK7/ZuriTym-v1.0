package com.zuritym.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.tabs.TabLayout;
import com.zuritym.app.R;
import com.zuritym.app.adapters.LeaderboardAdapter;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LeaderboardActivity extends AppCompatActivity {
    private RecyclerView rvLeaders;
    private LeaderboardAdapter adapter;
    private TextView tvMyRank;
    private ProgressBar progressBar;
    private TabLayout tabLayout;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_leaderboard);
        rvLeaders   = findViewById(R.id.rvLeaders);
        tvMyRank    = findViewById(R.id.tvMyRank);
        progressBar = findViewById(R.id.progressBar);
        tabLayout   = findViewById(R.id.tabLayout);
        rvLeaders.setLayoutManager(new LinearLayoutManager(this));
        adapter = new LeaderboardAdapter();
        rvLeaders.setAdapter(adapter);
        tabLayout.addOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
            @Override public void onTabSelected(TabLayout.Tab tab) {
                String period = tab.getPosition() == 0 ? "all" : tab.getPosition() == 1 ? "weekly" : "monthly";
                loadLeaderboard(period);
            }
            @Override public void onTabUnselected(TabLayout.Tab tab) {}
            @Override public void onTabReselected(TabLayout.Tab tab) {}
        });
        loadLeaderboard("all");
    }

    private void loadLeaderboard(String period) {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().getLeaderboard(period).enqueue(new Callback<ApiResponse<LeaderboardData>>() {
            @Override public void onResponse(Call<ApiResponse<LeaderboardData>> c, Response<ApiResponse<LeaderboardData>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    adapter.setData(r.body().data.leaders);
                    tvMyRank.setText("Your Rank: #" + r.body().data.myRank);
                }
            }
            @Override public void onFailure(Call<ApiResponse<LeaderboardData>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }
}
