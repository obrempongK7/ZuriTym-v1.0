package com.zuritym.app.adapters;
import android.content.Context;
import android.content.Intent;
import android.view.*;
import android.widget.*;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.activities.TaskDetailActivity;
import com.zuritym.app.models.TaskData;
import com.zuritym.app.utils.AppUtils;
import java.util.ArrayList;
import java.util.List;

public class TaskAdapter extends RecyclerView.Adapter<TaskAdapter.VH> {
    private List<TaskData> items = new ArrayList<>();
    private Context context;
    public TaskAdapter(Context ctx) { context = ctx; }
    public void setData(List<TaskData> data) { items = data != null ? data : new ArrayList<>(); notifyDataSetChanged(); }
    @NonNull @Override public VH onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        return new VH(LayoutInflater.from(parent.getContext()).inflate(R.layout.item_task, parent, false));
    }
    @Override public void onBindViewHolder(@NonNull VH h, int pos) {
        TaskData t = items.get(pos);
        h.tvTitle.setText(t.title);
        h.tvReward.setText("+" + (int)t.rewardPoints + " pts");
        h.tvType.setText(t.type.replace("_"," ").toUpperCase());
        h.tvLimit.setText(t.todayCompleted + "/" + t.dailyLimit + " today");
        h.itemView.setAlpha(t.isCompletable ? 1f : 0.5f);
        h.itemView.setOnClickListener(v -> {
            Intent i = new Intent(context, TaskDetailActivity.class);
            i.putExtra("task_id", t.id);
            context.startActivity(i);
        });
    }
    @Override public int getItemCount() { return items.size(); }
    static class VH extends RecyclerView.ViewHolder {
        TextView tvTitle, tvReward, tvType, tvLimit;
        VH(View v) { super(v); tvTitle=v.findViewById(R.id.tvTitle); tvReward=v.findViewById(R.id.tvReward); tvType=v.findViewById(R.id.tvType); tvLimit=v.findViewById(R.id.tvLimit); }
    }
}
