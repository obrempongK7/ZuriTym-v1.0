package com.zuritym.app.adapters;
import android.view.*;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.models.ChatMessage;
import java.util.ArrayList;
import java.util.List;

public class ChatAdapter extends RecyclerView.Adapter<ChatAdapter.VH> {
    private List<ChatMessage> items = new ArrayList<>();
    private final int myUserId;
    private static final int VIEW_SENT = 1, VIEW_RECEIVED = 2;
    public ChatAdapter(int myId) { myUserId = myId; }
    public void setData(List<ChatMessage> d) { items = d != null ? d : new ArrayList<>(); notifyDataSetChanged(); }
    public void addMessage(ChatMessage m) { items.add(m); notifyItemInserted(items.size()-1); }
    @Override public int getItemViewType(int pos) { return (items.get(pos).user != null && items.get(pos).user.id == myUserId) ? VIEW_SENT : VIEW_RECEIVED; }
    @NonNull @Override public VH onCreateViewHolder(@NonNull ViewGroup p, int t) {
        int layout = t == VIEW_SENT ? R.layout.item_chat_sent : R.layout.item_chat_received;
        return new VH(LayoutInflater.from(p.getContext()).inflate(layout, p, false));
    }
    @Override public void onBindViewHolder(@NonNull VH h, int pos) {
        ChatMessage m = items.get(pos);
        h.tvMessage.setText(m.message);
        if (h.tvName != null && m.user != null) h.tvName.setText(m.user.name);
        if (h.tvTime != null) h.tvTime.setText(m.time);
    }
    @Override public int getItemCount() { return items.size(); }
    static class VH extends RecyclerView.ViewHolder {
        TextView tvMessage, tvName, tvTime;
        VH(View v) { super(v); tvMessage=v.findViewById(R.id.tvMessage); tvName=v.findViewById(R.id.tvName); tvTime=v.findViewById(R.id.tvTime); }
    }
}
