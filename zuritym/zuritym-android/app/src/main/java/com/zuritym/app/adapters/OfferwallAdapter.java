package com.zuritym.app.adapters;
import android.view.*;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.models.OfferwallData;
import java.util.ArrayList;
import java.util.List;

public class OfferwallAdapter extends RecyclerView.Adapter<OfferwallAdapter.VH> {
    public interface OnClickListener { void onClick(OfferwallData wall); }
    private List<OfferwallData> items = new ArrayList<>();
    private final OnClickListener listener;
    public OfferwallAdapter(OnClickListener l) { listener = l; }
    public void setData(List<OfferwallData> d) { items = d != null ? d : new ArrayList<>(); notifyDataSetChanged(); }
    @NonNull @Override public VH onCreateViewHolder(@NonNull ViewGroup p, int t) { return new VH(LayoutInflater.from(p.getContext()).inflate(R.layout.item_offerwall, p, false)); }
    @Override public void onBindViewHolder(@NonNull VH h, int pos) {
        OfferwallData w = items.get(pos);
        h.tvName.setText(w.name);
        h.tvType.setText(w.type.toUpperCase());
        h.itemView.setOnClickListener(v -> listener.onClick(w));
    }
    @Override public int getItemCount() { return items.size(); }
    static class VH extends RecyclerView.ViewHolder {
        TextView tvName, tvType;
        VH(View v) { super(v); tvName=v.findViewById(R.id.tvName); tvType=v.findViewById(R.id.tvType); }
    }
}
