package com.zuritym.app.fragments;

import android.os.Bundle;
import android.view.*;
import android.widget.ProgressBar;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.adapters.TaskAdapter;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.List;

public class TasksFragment extends Fragment {
    private RecyclerView rvTasks;
    private TaskAdapter adapter;
    private ProgressBar progressBar;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_tasks, container, false);
        rvTasks     = v.findViewById(R.id.rvTasks);
        progressBar = v.findViewById(R.id.progressBar);
        rvTasks.setLayoutManager(new LinearLayoutManager(getContext()));
        adapter = new TaskAdapter(getContext());
        rvTasks.setAdapter(adapter);
        loadTasks();
        return v;
    }

    private void loadTasks() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().getTasks(null).enqueue(new Callback<ApiResponse<List<TaskData>>>() {
            @Override public void onResponse(Call<ApiResponse<List<TaskData>>> c, Response<ApiResponse<List<TaskData>>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) adapter.setData(r.body().data);
            }
            @Override public void onFailure(Call<ApiResponse<List<TaskData>>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }
}
