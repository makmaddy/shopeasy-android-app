package blueappsoftware.shopeasy;

import android.app.Application;
import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;

/**
 * Created by kamal_bunkar on 19-12-2017.
 */

public class MyApp extends Application {
    private static Context context;
    private String TAG ="myApp";
    @Override
    public void onCreate() {
        super.onCreate();
        context = getApplicationContext();
       // Log.e(TAG, "  myapp stater");
    }

    public static Context getContext(){
        return context;
    }
}
