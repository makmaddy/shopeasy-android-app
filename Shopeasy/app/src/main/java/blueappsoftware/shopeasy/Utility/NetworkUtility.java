package blueappsoftware.shopeasy.Utility;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

/**
 * Created by kamal_bunkar on 29-12-2017.
 */

public class NetworkUtility  {

    public static Boolean isNetworkConnected(Context mContext){

        ConnectivityManager cm= (ConnectivityManager) mContext.getSystemService(mContext.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetwork = cm.getActiveNetworkInfo();
        return activeNetwork != null && activeNetwork.isConnectedOrConnecting();

    }
}
