package com.zuritym.app.activities;

import android.os.Bundle;
import android.webkit.*;
import android.widget.ProgressBar;
import androidx.appcompat.app.AppCompatActivity;
import com.zuritym.app.R;

public class WebViewActivity extends AppCompatActivity {
    public static final String EXTRA_URL   = "url";
    public static final String EXTRA_TITLE = "title";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_webview);
        String url   = getIntent().getStringExtra(EXTRA_URL);
        String title = getIntent().getStringExtra(EXTRA_TITLE);
        if (title != null && getSupportActionBar() != null) getSupportActionBar().setTitle(title);

        WebView webView     = findViewById(R.id.webView);
        ProgressBar progress = findViewById(R.id.progressBar);

        webView.setWebViewClient(new WebViewClient());
        WebSettings ws = webView.getSettings();
        ws.setJavaScriptEnabled(true);
        ws.setDomStorageEnabled(true);
        ws.setLoadWithOverviewMode(true);
        ws.setUseWideViewPort(true);
        webView.setWebChromeClient(new WebChromeClient() {
            @Override public void onProgressChanged(WebView view, int newProgress) {
                progress.setProgress(newProgress);
                if (newProgress == 100) progress.setVisibility(android.view.View.GONE);
                else progress.setVisibility(android.view.View.VISIBLE);
            }
        });
        if (url != null) webView.loadUrl(url);
    }
}
