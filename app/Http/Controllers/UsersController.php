<?php

namespace App\Http\Controllers;

use App\Mail\Demoemail;
use App\Models\forgetPasswordOtp;
use App\Models\notifications;
use App\Models\order;
use App\Models\SmsOtp;
use App\Models\User;
use Exception;
use Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users =  User::all();
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function verifyOtp(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'otp' => 'required|size:6',
                'email' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'All feilds are required * .. !',
                'errors' => $validateUser->errors()
            ], 402);
        }

        $email = $request->email;
        $otp = $request->otp;
        $dbcheck = SmsOtp::where('email', '=', $email)->where('otp', '=', $otp)->get()->first();

        if ($dbcheck) {
            if ($dbcheck['expireDate'] < strtotime('now')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Otp has been expired..'
                ], 410);
            }
            return response()->json([
                'status' => true,
                'message' => 'Otp matched successfully..'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => "Otp didn't matched (try again..)",
        ], 401);
    }

    public function ChangePassword(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'contactNo' => 'required',
                'fpassword' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'All feilds are required * .. !',
                'errors' => $validateUser->errors()
            ], 402);
        }

        $contactNo = $request->contactNo;
        $fpassword = $request->fpassword;

        if (User::where(['contactNo' => $contactNo])->first()) {
            User::where(['contactNo' => $contactNo])->first()->update(['password' => $fpassword]);
            return response()->json([
                'status' => true,
                'message' => 'Password has been changed successfully..',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No user of this contact.. pls check'
            ], 402);
        }
    }

    public function verifySmsOtp(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'otp' => 'required|size:6',
                'contactNo' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'All feilds are required * .. !',
                'errors' => $validateUser->errors()
            ], 402);
        }

        $contactNo = $request->contactNo;
        $otp = $request->otp;
        $dbcheck = forgetPasswordOtp::where('contactNo', '=', $contactNo)->where('otp', '=', $otp)->get()->first();

        if ($dbcheck) {
            if ($dbcheck['expireDate'] < strtotime('now')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Otp has been expired..'
                ], 410);
            }
            return response()->json([
                'status' => true,
                'message' => 'Otp matched successfully..'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => "Otp didn't matched (try again..)",
        ], 401);
    }


    public function sendEmail(Request $request)
    {
        $user =  User::where(['email' => $request->email])->first();
        if ($user) {
            return response()->json([
                'status' => false,
                'message' => 'Email already exist..'
            ], 401);
        }
        $sendMailto = $request->email;
        $name = $request->name;
        $otp = random_int(100000, 999999);
        try {
            if (!SmsOtp::where(['email' => $sendMailto])->first()) {
                $dbotp = SmsOtp::create([
                    'otp' => $otp,
                    'email' => $sendMailto,
                    'expireDate' => strtotime("+10 minutes"),
                ]);
            } else {
                $dbotp = SmsOtp::where(['email' => $sendMailto])->first()->update([
                    'otp' => $otp,
                    'expireDate' => strtotime("+10 minutes"),
                ]);
            }
            Mail::to($sendMailto)->send(new Demoemail($otp, $name, "Emailview"));
            return response()->json([
                'status' => true,
                'email' => $sendMailto,
                'message' => 'Please check your mail box..'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'invalid email'
            ], 200);
        }
    }
    public function sendsmsotp(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'contactNo' => 'required|size:10',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'All feilds are required * .. !',
                'errors' => $validateUser->errors()
            ], 402);
        }

        $user =  User::where('contactNo', "=", $request->contactNo)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'You are not a user..'
            ], 401);
        }
        $sendSmsto = $request->contactNo;
        $otp = random_int(100000, 999999);
        try {
            $ContactNo = "+91$request->contactNo";
            // $account_sid = getenv("TWILIO_SID");
            // $auth_token = getenv("TWILIO_AUTH_TOKEN");
            // $twilio_number = getenv("TWILIO_NUMBER");
            // $client = new Client($account_sid, $auth_token);
            // $message= "OTP for Forget Password is $otp it will work only upto 10 minutes.";
            if ($ContactNo == "+918155077546") {
                // $client->messages->create($ContactNo, 
                // ['from' => $twilio_number, 'body' => $message] );
            } else {
                return response()->json([
                    'status' => false,
                    'contactNo' => $otp,
                    'message' => 'This number is not linked with tiwilo account..'
                ], 401);
            }
            if (!forgetPasswordOtp::where(['contactNo' => $sendSmsto])->first()) {
                $dbotp = forgetPasswordOtp::create([
                    'otp' => $otp,
                    'contactNo' => $sendSmsto,
                    'expireDate' => strtotime("+10 minutes"),
                ]);
            } else {
                $dbotp = forgetPasswordOtp::where(['contactNo' => $sendSmsto])->first()->update([
                    'otp' => $otp,
                    'expireDate' => strtotime("+10 minutes"),
                ]);
            }
            return response()->json([
                'status' => true,
                'contactNo' => $otp,
                'message' => 'Please check for Sms..'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'invalid number..'
            ], 401);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'contactNo' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'All feilds are required * .. !',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $token = Str::random(60);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'contactNo' => $request->contactNo,
                "Token" => $token,
                "status" => 0,
            ]);

            $sendMailto = $request->email;
            $name = $request->name;
            try {
                Mail::to($sendMailto)->send(new Demoemail($token, $name, "Emailview"));
                return response()->json([
                    'status' => true,
                    'email' => $sendMailto,
                    'message' => 'Please check your mail box to verify email..'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid email'
                ], 200);
            }
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'User Created Successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }



    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => "All feild's are required",
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $credentials = $request->only('email', 'password');
            if (Auth::guard('web')->attempt($credentials)) {
                $user = Auth::guard('web')->user();
                $token =  Str::random(60);
                $status = User::whereId($user->id)->select("status")->first();
                if ($status->status == '0') {
                    return response()->json([
                        'status' => true,
                        'message' => "Email is not verified..!",
                    ], 401);
                }
                $token = $user->createToken("Token")->plainTextToken;
                User::whereId($user->id)->update(['Token' => $token]);
                $wishlist = User::leftJoin('wishlists', "users.id", "=", "wishlists.userId")
                    ->where('users.id', '=', $user->id)
                    ->get(['wishlists.productId as wishlist'])->first();
                $wishlist = array_map('intval', explode(',', $wishlist->wishlist));
                try {
                    notifications::create([
                        'userId' => $user->id,
                        'activity' => 'login',
                        'message' => "login to application",
                        'icon' => "login",
                    ]);
                }catch(Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage()
                    ], 401);
                }
                return response()->json([
                    'status' => true,
                    "user" => $user,
                    "wishlist" => $wishlist,
                    'message' => 'User Logged In Successfully',
                    'token' => $token
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Invalid email Or password..',
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function adminLogin(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => "All feild's are required",
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $credentials = $request->only('email', 'password');
            if (Auth::guard('web')->attempt($credentials)) {
                $user = Auth::guard('web')->user();
                $token = $user->createToken("Token")->plainTextToken;
                $status = User::whereId($user->id)->select("status", "isAdmin")->first();
                if ($status->status == '0') {
                    return response()->json([
                        'status' => true,
                        'message' => "Email is not verified..!",
                    ], 401);
                }
                if ($status->isAdmin == '0') {
                    return response()->json([
                        'status' => true,
                        'message' => "Email is not verified for Admin..!",
                    ], 401);
                }
                User::whereId($user->id)->update(['Token' => $token]);
                $wishlist = User::leftJoin('wishlists', "users.id", "=", "wishlists.userId")
                    ->where('users.id', '=', $user->id)
                    ->get(['wishlists.productId as wishlist'])->first();
                $wishlist = array_map('intval', explode(',', $wishlist->wishlist));
                return response()->json([
                    'status' => true,
                    "user" => $user,
                    'message' => 'User Logged In Successfully',
                    'token' => $token,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Invalid email Or password..',
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        try {
            return  Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $finduser = User::where('social_id', $user->id)->first();
            if ($finduser) {
                Auth::login($finduser);
                $wishlist = User::leftJoin('wishlists', "users.id", "=", "wishlists.userId")
                    ->where('users.id', '=', $finduser->id)
                    ->get(['wishlists.productId as wishlist'])->first();
                $wishlist = array_map('intval', explode(',', $wishlist->wishlist));
                notifications::create([
                    'userId' => $finduser->id,
                    'activity' => 'login',
                    'message' => "login to application",
                    'icon' => "login",
                ]);
                return response()->json([
                    'status' => true,
                    "user" => $finduser,
                    "wishlist" => $wishlist,
                    'message' => 'User Logged In Successfully',
                    'token' => $finduser->createToken('token')->plainTextToken
                ], 200);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->avatar,
                    'social_id' => $user->id,
                    'social_type' => 'google',
                    'password' => encrypt('my-google')
                ]);

                Auth::login($newUser);
                $wishlist = User::leftJoin('wishlists', "users.id", "=", "wishlists.userId")
                    ->where('users.id', '=', $newUser->id)
                    ->get(['wishlists.productId as wishlist'])->first();
                $wishlist = array_map('intval', explode(',', $wishlist->wishlist));
                notifications::create([
                    'userId' => $user->id,
                    'activity' => 'login',
                    'message' => "login to application",
                    'icon' => "login",
                ]);
                return response()->json([
                    'status' => true,
                    "user" => $user,
                    "wishlist" => $wishlist,
                    'message' => 'User Logged In Successfully',
                    'token' => $newUser->createToken('token')->plainTextToken
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //Validated
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'address' => '',
                'contactNo' => 'required',
                'profile' => 'mimes:jpg,png,jpeg,webp',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        if ($request->profile) {
            if (file_exists(public_path("uploads/Users/user$user->id/"))) {
                if (file_exists(public_path("uploads/Users/user$user->id/"))) {
                    if ($user->profile != null) {
                        $old_profilename = basename($user->profile);
                        unlink(public_path("uploads/Users/user$user->id/$old_profilename"));
                    }
                }
                if ($user->profile != null) {
                    rmdir(public_path("uploads/Users/user$user->id"));
                }
            }
            $profilename = $request->profile->getClientOriginalName();
            $request->profile->move(public_path("uploads/Users/user$user->id/"), $profilename);
            $profilurl = ("uploads/Users/user$user->id/$profilename");
        } else {
            $profilurl = $user->profile;
        }
        try {
            $users =  User::whereId($user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'profile' => $profilurl,
                'contactNo' => $request->contactNo,
                'address' => $request->address
            ]);
            try {
                notifications::create([
                    'userId' => $user->id,
                    'activity' => 'update',
                    'message' => "update the profile",
                    'icon' => "update",
                ]);
            }catch(Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 401);
            }
            if ($users) {
                return response()->json([
                    'status' => true,
                    'message' => 'user successfully updated..',
                    'user' => $this->show($user->id),
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $e,
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $old_profile = basename($user->profile);
            if (file_exists(public_path("uploads/Users/user$user->id/$old_profile"))) {
                unlink(public_path("uploads/Users/user$user->id/$old_profile"));
                rmdir(public_path("uploads/Users/user$user->id"));
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'unable to delete file',
                'errors' => $e
            ], 405);
        }
        try {
            order::where('userId', $user->id)->delete();
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'User has deleted successfully!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not delete user!',
                "error" => $e,
            ], 500);
        }
    }

    public function addUser(Request $request)
    {
        $users = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'contactNo' => 'required',
                'profile' => 'mimes:jpg,png,jpeg,webp',
            ]
        );

        if ($users->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $users->errors(),
            ], 500);
        }

        ($request->address == null) ? $address = null : $address = $request->address;
        $token = Str::random(60);
        $id = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "contactNo" => $request->contactNo,
            "address" => $address,
            "token" => $token,
            "status" => 0,
        ])->id;
        $sendMailto = $request->email;
        $name = $request->name;
        try {
            if ($id) {
                Mail::to($sendMailto)->send(new Demoemail($token, $name, 'Emailview'));
                return response()->json([
                    'status' => true,
                    'email' => $sendMailto,
                    'message' => 'Please check your mail box to verify email..'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'invalid email'
                ], 200);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if ($request->profile) {
            try {
                $profilename = $request->profile->getClientOriginalName();
                $request->profile->move(public_path("uploads/Users/user$id/"), $profilename);
                $profilurl = ("uploads/Users/user$id/$profilename");
            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'unable to create file',
                    'errors' => $e,
                ], 422);
            }
            try {
                User::whereId($id)->update(['profile' => $profilurl]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'unable to insert profile data into Db',
                    'errors' => $e,
                ], 500);
            }
            return response()->json([
                'status' => true,
                'message' => 'User is added'
            ], 200);
        }
    }

    public function verifyToken($token)
    {
        try {
            $flag = User::where("Token", $token)->update(["status" => "1"]);
            return ($flag) ? redirect('VerifySuccess') : redirect('VerifyError');
            // return response()->json([
            //     'status' => true,
            //     'message' => 'User is verified'
            // ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to verify email',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus(Request $request, User $user)
    {
        try {

            $rule = Validator::make(
                $request->all(),
                [
                    'status' => 'required',
                ]
            );
            if ($rule->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $rule->errors(),
                ], 500);
            }

            $sattus = ($request->status) ? "1" : "0";
            $stat = User::whereId($user->id)->update(['status' => $sattus]);
            if ($stat) {
                return response()->json([
                    'status' => true,
                    'message' => 'Status has been updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Status has been updated'
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Not able to Update the status..',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
    public function changePrivilege(Request $request, User $user)
    {
        try {

            $rule = Validator::make(
                $request->all(),
                [
                    'status' => 'required',
                ]
            );
            if ($rule->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $rule->errors(),
                ], 500);
            }

            $sattus = ($request->status) ? "1" : "0";
            $stat = User::whereId($user->id)->update(['isAdmin' => $sattus]);
            if ($stat) {
                return response()->json([
                    'status' => true,
                    'message' => 'Privilege has been updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Not able to Update the privilege..'
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Not able to Update the privilege..',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout($id){
        try{
            User::where("id",$id)->update(["token"=>""]);
            notifications::create([
                'userId' => $id,
                'activity' => 'logout',
                'message' => "Logout to application",
                'icon' => "logout",
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Logout Out successfull',
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Not able to logout',
                'errors' => $e->getMessage(),
            ], 401);
        }    
    }
    
}
