package com.zuritym.app.adapters;
import android.view.*;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.models.TaskData;
import java.util.ArrayList;
import java.util.List;

public class FeaturedTaskAdapter extends RecyclerView.Adapter<FeaturedTaskAdapter.VH> {
    private List<TaskData> items = new ArrayList<>();
    public void setData(List<TaskData> d) { items = d != null ? d : new ArrayList<>(); notifyDataSetChanged(); }
    @NonNull @Override public VH onCreateViewHolder(@NonNull ViewGroup p, int t) { return new VH(LayoutInflater.from(p.getContext()).inflate(R.layout.item_featured_task, p, false)); }
    @Override public void onBindViewHolder(@NonNull VH h, int pos) {
        TaskData t = items.get(pos);
        h.tvTitle.setText(t.title);
        h.tvReward.setText("+" + (int)t.rewardPoints + " pts");
    }
    @Override public int getItemCount() { return items.size(); }
    static class VH extends RecyclerView.ViewHolder {
        TextView tvTitle, tvReward;
        VH(View v) { super(v); tvTitle=v.findViewById(R.id.tvTitle); tvReward=v.findViewById(R.id.tvReward); }
    }
}
