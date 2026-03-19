package com.zuritym.app.adapters;
import android.view.*;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.zuritym.app.R;
import com.zuritym.app.models.LeaderboardData;
import com.zuritym.app.utils.AppUtils;
import de.hdodenhof.circleimageview.CircleImageView;
import java.util.ArrayList;
import java.util.List;

public class LeaderboardAdapter extends RecyclerView.Adapter<LeaderboardAdapter.VH> {
    private List<LeaderboardData.LeaderEntry> items = new ArrayList<>();
    public void setData(List<LeaderboardData.LeaderEntry> d) { items = d != null ? d : new ArrayList<>(); notifyDataSetChanged(); }
    @NonNull @Override public VH onCreateViewHolder(@NonNull ViewGroup p, int t) { return new VH(LayoutInflater.from(p.getContext()).inflate(R.layout.item_leaderboard, p, false)); }
    @Override public void onBindViewHolder(@NonNull VH h, int pos) {
        LeaderboardData.LeaderEntry e = items.get(pos);
        h.tvRank.setText("#" + e.rank);
        h.tvName.setText(e.name);
        h.tvPoints.setText(AppUtils.formatPoints(e.totalPoints));
        if (e.avatarUrl != null) Glide.with(h.itemView.getContext()).load(e.avatarUrl).placeholder(R.drawable.ic_avatar).into(h.ivAvatar);
    }
    @Override public int getItemCount() { return items.size(); }
    static class VH extends RecyclerView.ViewHolder {
        TextView tvRank, tvName, tvPoints; CircleImageView ivAvatar;
        VH(View v) { super(v); tvRank=v.findViewById(R.id.tvRank); tvName=v.findViewById(R.id.tvName); tvPoints=v.findViewById(R.id.tvPoints); ivAvatar=v.findViewById(R.id.ivAvatar); }
    }
}
