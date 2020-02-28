package blueappsoftware.shopeasy.login;

import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import blueappsoftware.shopeasy.R;
import blueappsoftware.shopeasy.Utility.AppUtilits;
import blueappsoftware.shopeasy.Utility.Constant;
import blueappsoftware.shopeasy.Utility.DataValidation;
import blueappsoftware.shopeasy.Utility.NetworkUtility;
import blueappsoftware.shopeasy.Utility.SharePreferenceUtils;
import blueappsoftware.shopeasy.WebServices.ServiceWrapper;
import blueappsoftware.shopeasy.beanResponse.ForgotPassword;
import blueappsoftware.shopeasy.home.HomeActivity;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Created by kamal_bunkar on 19-12-2017.
 */

public class ForgotPasswordActivity extends AppCompatActivity {
    private String TAG ="FOrgotPassword";
    private EditText phoneno;
    private TextView submit;
    private LinearLayout layout_signin;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);

        phoneno = (EditText) findViewById(R.id.phone_number);
        submit = (TextView) findViewById(R.id.submit);
        layout_signin = (LinearLayout) findViewById(R.id.layout_signin);

        submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if (DataValidation.isValidPhoneNumber(phoneno.getText().toString())){
                    /// show error pupup
                    AppUtilits.displayMessage(ForgotPasswordActivity.this, getString(R.string.phone_no) + " "+ getString(R.string.is_invalid));
                }else {

                    callForgotPasswordAPI();

                }


            }
        });

        layout_signin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                finish();
            }
        });
    }

    public void callForgotPasswordAPI(){
        if (!NetworkUtility.isNetworkConnected(ForgotPasswordActivity.this)){
            AppUtilits.displayMessage(ForgotPasswordActivity.this,  getString(R.string.network_not_connected));

        }else {

            ServiceWrapper serviceWrapper = new ServiceWrapper(null);
            Call<ForgotPassword>  call = serviceWrapper.UserForgotPassword(phoneno.getText().toString());
            call.enqueue(new Callback<ForgotPassword>() {
                @Override
                public void onResponse(Call<ForgotPassword> call, Response<ForgotPassword> response) {

                    Log.d(TAG, "reponse : "+ response.toString());
                    if (response.body()!= null && response.isSuccessful()){
                        if (response.body().getStatus() ==1){

                           // response.body().getInformation().getOtp()
                            // start home activity
                            Intent intent = new Intent(ForgotPasswordActivity.this, OTP_varify.class);
                            intent.putExtra("userid", response.body().getInformation().getUserId() );
                            intent.putExtra("otp", response.body().getInformation().getOtp());
                            intent.putExtra("phoneno", phoneno.getText().toString());

                            startActivity(intent);


                        }else {
                            AppUtilits.displayMessage(ForgotPasswordActivity.this,  response.body().getMsg());
                        }
                    }else {
                        AppUtilits.displayMessage(ForgotPasswordActivity.this,  getString(R.string.failed_request));

                    }
                }

                @Override
                public void onFailure(Call<ForgotPassword> call, Throwable t) {

                   // Log.e(TAG, " failure "+ t.toString());
                    AppUtilits.displayMessage(ForgotPasswordActivity.this,  getString(R.string.failed_request));

                }
            });


        }



        }

}
