package com.zuritym.app.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.*;
import androidx.fragment.app.Fragment;
import com.zuritym.app.R;
import com.zuritym.app.activities.*;

public class EarnFragment extends Fragment {
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View v = inflater.inflate(R.layout.fragment_earn, container, false);
        v.findViewById(R.id.cardSpin).setOnClickListener(x -> startActivity(new Intent(getContext(), SpinActivity.class)));
        v.findViewById(R.id.cardScratch).setOnClickListener(x -> startActivity(new Intent(getContext(), ScratchCardActivity.class)));
        v.findViewById(R.id.cardOfferwall).setOnClickListener(x -> startActivity(new Intent(getContext(), OfferwallActivity.class)));
        v.findViewById(R.id.cardLeaderboard).setOnClickListener(x -> startActivity(new Intent(getContext(), LeaderboardActivity.class)));
        v.findViewById(R.id.cardChat).setOnClickListener(x -> startActivity(new Intent(getContext(), ChatActivity.class)));
        return v;
    }
}
