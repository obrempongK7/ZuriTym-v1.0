package com.zuritym.app.adapters;
import android.graphics.Color;
import android.view.*;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.models.TransactionData;
import java.util.ArrayList;
import java.util.List;

public class TransactionAdapter extends RecyclerView.Adapter<TransactionAdapter.VH> {
    private List<TransactionData> items = new ArrayList<>();
    public void setData(List<TransactionData> d) { items = d != null ? d : new ArrayList<>(); notifyDataSetChanged(); }
    @NonNull @Override public VH onCreateViewHolder(@NonNull ViewGroup p, int t) { return new VH(LayoutInflater.from(p.getContext()).inflate(R.layout.item_transaction, p, false)); }
    @Override public void onBindViewHolder(@NonNull VH h, int pos) {
        TransactionData t = items.get(pos);
        h.tvDesc.setText(t.description);
        h.tvAmount.setText(t.formatted);
        h.tvAmount.setTextColor(t.amount >= 0 ? Color.parseColor("#2ecc71") : Color.parseColor("#e74c3c"));
        h.tvDate.setText(t.createdAt);
        h.tvType.setText(t.type.replace("_"," ").toUpperCase());
    }
    @Override public int getItemCount() { return items.size(); }
    static class VH extends RecyclerView.ViewHolder {
        TextView tvDesc, tvAmount, tvDate, tvType;
        VH(View v) { super(v); tvDesc=v.findViewById(R.id.tvDesc); tvAmount=v.findViewById(R.id.tvAmount); tvDate=v.findViewById(R.id.tvDate); tvType=v.findViewById(R.id.tvType); }
    }
}
