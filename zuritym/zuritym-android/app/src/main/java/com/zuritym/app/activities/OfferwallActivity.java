package com.zuritym.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ProgressBar;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.adapters.OfferwallAdapter;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.List;

public class OfferwallActivity extends AppCompatActivity {
    private RecyclerView rvOfferwalls;
    private OfferwallAdapter adapter;
    private ProgressBar progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_offerwall);
        rvOfferwalls = findViewById(R.id.rvOfferwalls);
        progressBar  = findViewById(R.id.progressBar);
        rvOfferwalls.setLayoutManager(new LinearLayoutManager(this));
        adapter = new OfferwallAdapter(wall -> {
            Intent i = new Intent(this, WebViewActivity.class);
            i.putExtra(WebViewActivity.EXTRA_URL, wall.url);
            i.putExtra(WebViewActivity.EXTRA_TITLE, wall.name);
            startActivity(i);
        });
        rvOfferwalls.setAdapter(adapter);
        loadOfferwalls();
    }

    private void loadOfferwalls() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().getOfferwalls().enqueue(new Callback<ApiResponse<List<OfferwallData>>>() {
            @Override public void onResponse(Call<ApiResponse<List<OfferwallData>>> c, Response<ApiResponse<List<OfferwallData>>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) adapter.setData(r.body().data);
            }
            @Override public void onFailure(Call<ApiResponse<List<OfferwallData>>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }
}
