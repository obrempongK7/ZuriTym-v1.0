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

public class TaskDetailActivity extends AppCompatActivity {
    private int taskId;
    private TaskData task;
    private int userTaskId = -1;
    private Button btnStartComplete;
    private TextView tvTitle, tvDescription, tvReward, tvTimer, tvStatus;
    private ProgressBar progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_task_detail);
        taskId           = getIntent().getIntExtra("task_id", -1);
        tvTitle          = findViewById(R.id.tvTitle);
        tvDescription    = findViewById(R.id.tvDescription);
        tvReward         = findViewById(R.id.tvReward);
        tvTimer          = findViewById(R.id.tvTimer);
        tvStatus         = findViewById(R.id.tvStatus);
        btnStartComplete = findViewById(R.id.btnStartComplete);
        progressBar      = findViewById(R.id.progressBar);
        btnStartComplete.setOnClickListener(v -> {
            if (task == null) return;
            if (userTaskId < 0) startTask();
            else completeTask();
        });
        loadTask();
    }

    private void loadTask() {
        // Task is passed via intent extras; fetch if needed
        ApiClient.getApiService().getTasks(null).enqueue(new Callback<ApiResponse<java.util.List<TaskData>>>() {
            @Override public void onResponse(Call<ApiResponse<java.util.List<TaskData>>> c, Response<ApiResponse<java.util.List<TaskData>>> r) {
                if (r.isSuccessful() && r.body() != null) {
                    for (TaskData t : r.body().data) { if (t.id == taskId) { task = t; bindTask(); break; } }
                }
            }
            @Override public void onFailure(Call<ApiResponse<java.util.List<TaskData>>> c, Throwable t) {}
        });
    }

    private void bindTask() {
        tvTitle.setText(task.title);
        tvDescription.setText(task.description);
        tvReward.setText("+" + (int)task.rewardPoints + " pts");
        tvTimer.setText(task.timerSeconds > 0 ? "Timer: " + task.timerSeconds + "s" : "No timer");
        btnStartComplete.setText(task.isCompletable ? "Start Task" : "Limit Reached");
        btnStartComplete.setEnabled(task.isCompletable);
    }

    private void startTask() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().startTask(taskId).enqueue(new Callback<ApiResponse<TaskStartData>>() {
            @Override public void onResponse(Call<ApiResponse<TaskStartData>> c, Response<ApiResponse<TaskStartData>> r) {
                progressBar.setVisibility(View.GONE);
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    userTaskId = r.body().data.userTaskId;
                    btnStartComplete.setText("Complete Task");
                    tvStatus.setText("Task started! Complete the action then tap Complete.");
                    tvStatus.setVisibility(View.VISIBLE);
                    if (r.body().data.actionUrl != null && !r.body().data.actionUrl.isEmpty()) {
                        android.content.Intent i = new android.content.Intent(TaskDetailActivity.this, WebViewActivity.class);
                        i.putExtra(WebViewActivity.EXTRA_URL, r.body().data.actionUrl);
                        startActivity(i);
                    }
                } else AppUtils.showToast(TaskDetailActivity.this, r.body() != null ? r.body().message : "Error");
            }
            @Override public void onFailure(Call<ApiResponse<TaskStartData>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }

    private void completeTask() {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().completeTask(userTaskId, null).enqueue(new Callback<ApiResponse<TaskCompleteData>>() {
            @Override public void onResponse(Call<ApiResponse<TaskCompleteData>> c, Response<ApiResponse<TaskCompleteData>> r) {
                progressBar.setVisibility(View.GONE);
                AppUtils.showToast(TaskDetailActivity.this, r.body() != null ? r.body().message : "Done!");
                if (r.isSuccessful() && r.body() != null && r.body().success) finish();
            }
            @Override public void onFailure(Call<ApiResponse<TaskCompleteData>> c, Throwable t) { progressBar.setVisibility(View.GONE); }
        });
    }
}
