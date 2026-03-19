package com.zuritym.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.zuritym.app.R;
import com.zuritym.app.adapters.ChatAdapter;
import com.zuritym.app.models.*;
import com.zuritym.app.network.ApiClient;
import com.zuritym.app.utils.PrefManager;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.util.HashMap;
import java.util.List;

public class ChatActivity extends AppCompatActivity {
    private RecyclerView rvMessages;
    private EditText etMessage;
    private ImageButton btnSend;
    private ChatAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_chat);
        rvMessages = findViewById(R.id.rvMessages);
        etMessage  = findViewById(R.id.etMessage);
        btnSend    = findViewById(R.id.btnSend);
        rvMessages.setLayoutManager(new LinearLayoutManager(this));
        adapter = new ChatAdapter(PrefManager.getUser() != null ? PrefManager.getUser().id : -1);
        rvMessages.setAdapter(adapter);
        btnSend.setOnClickListener(v -> sendMessage());
        loadMessages();
    }

    private void loadMessages() {
        ApiClient.getApiService().getChatMessages().enqueue(new Callback<ApiResponse<List<ChatMessage>>>() {
            @Override public void onResponse(Call<ApiResponse<List<ChatMessage>>> c, Response<ApiResponse<List<ChatMessage>>> r) {
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    adapter.setData(r.body().data);
                    rvMessages.scrollToPosition(adapter.getItemCount() - 1);
                }
            }
            @Override public void onFailure(Call<ApiResponse<List<ChatMessage>>> c, Throwable t) {}
        });
    }

    private void sendMessage() {
        String msg = etMessage.getText().toString().trim();
        if (msg.isEmpty()) return;
        etMessage.setText("");
        HashMap<String, String> body = new HashMap<>();
        body.put("message", msg);
        ApiClient.getApiService().sendMessage(body).enqueue(new Callback<ApiResponse<ChatMessage>>() {
            @Override public void onResponse(Call<ApiResponse<ChatMessage>> c, Response<ApiResponse<ChatMessage>> r) {
                if (r.isSuccessful() && r.body() != null && r.body().success) {
                    adapter.addMessage(r.body().data);
                    rvMessages.scrollToPosition(adapter.getItemCount() - 1);
                }
            }
            @Override public void onFailure(Call<ApiResponse<ChatMessage>> c, Throwable t) {}
        });
    }
}
