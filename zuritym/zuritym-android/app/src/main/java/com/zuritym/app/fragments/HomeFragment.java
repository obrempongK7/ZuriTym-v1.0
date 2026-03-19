package com.zuritym.app.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.*;
import android.widget.*;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.activities.*;
import com.zuritym.app.adapters.FeaturedTaskAdapter;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.AppUtils;
import com.zuritym.app.utils.PrefManager;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class HomeFragment extends Fragment {
    private TextView tvGreeting, tvBalance, tvRank, tvAnnouncement;
    private RecyclerView rvFeaturedTasks;
    private FeaturedTaskAdapter taskAdapter;
    private View btnSpin, btnScratch, btnOfferwall, btnLeaderboard;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_home, container, false);
        tvGreeting     = v.findViewById(R.id.tvGreeting);
        tvBalance      = v.findViewById(R.id.tvBalance);
        tvRank         = v.findViewById(R.id.tvRank);
        tvAnnouncement = v.findViewById(R.id.tvAnnouncement);
        rvFeaturedTasks = v.findViewById(R.id.rvFeaturedTasks);
        btnSpin        = v.findViewById(R.id.btnSpin);
        btnScratch     = v.findViewById(R.id.btnScratch);
        btnOfferwall   = v.findViewById(R.id.btnOfferwall);
        btnLeaderboard = v.findViewById(R.id.btnLeaderboard);
        rvFeaturedTasks.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        taskAdapter = new FeaturedTaskAdapter();
        rvFeaturedTasks.setAdapter(taskAdapter);
        setupClickListeners();
        loadHome();
        return v;
    }

    private void setupClickListeners() {
        btnSpin.setOnClickListener(v -> startActivity(new Intent(getContext(), SpinActivity.class)));
        btnScratch.setOnClickListener(v -> startActivity(new Intent(getContext(), ScratchCardActivity.class)));
        btnOfferwall.setOnClickListener(v -> startActivity(new Intent(getContext(), OfferwallActivity.class)));
        btnLeaderboard.setOnClickListener(v -> startActivity(new Intent(getContext(), LeaderboardActivity.class)));
    }

    private void loadHome() {
        UserData user = PrefManager.getUser();
        if (user != null) {
            tvGreeting.setText(AppUtils.getGreeting() + ", " + user.name + "!");
            tvBalance.setText(AppUtils.formatPoints(user.wallet != null ? user.wallet.balance : 0));
            tvRank.setText("Rank #" + user.rank);
        }
        ApiClient.getApiService().getHome().enqueue(new Callback<ApiResponse<HomeData>>() {
            @Override public void onResponse(Call<ApiResponse<HomeData>> c, Response<ApiResponse<HomeData>> r) {
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    HomeData data = r.body().data;
                    if (data.announcement != null && !data.announcement.isEmpty()) {
                        tvAnnouncement.setVisibility(View.VISIBLE);
                        tvAnnouncement.setText(data.announcement);
                    }
                    tvBalance.setText(AppUtils.formatPoints(data.user.balance));
                    tvRank.setText("Rank #" + data.user.rank);
                    taskAdapter.setData(data.featuredTasks);
                }
            }
            @Override public void onFailure(Call<ApiResponse<HomeData>> c, Throwable t) {}
        });
    }
}
