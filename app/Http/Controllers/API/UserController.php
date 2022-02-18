<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Auth;
use Storage;
use Illuminate\Support\Str;
use Mail;
use App\Mail\NotifyMail;
use App\Http\Requests\UserRegisterRequest;

class UserController extends Controller
{

    
    public function sendInvite(UserRegisterRequest $request)
    {	
	
		$token_str = Str::random(40);
		if($request->email){
			$user = User::create([
                    'email' => $request->email,
                    'password' => bcrypt(123456),
					'token'=> $token_str
			]);
			$mailData = [
				'title' => 'Demo Email',
				'token' => $token_str
			];
		
			$response ='';
			Mail::to($request->email)->send(new NotifyMail($mailData));
			
			$emailResponse =''; 
			if (Mail::failures()) {
			   $emailResponse = 'Sorry! Please try again latter';
			}else{
			   $emailResponse = 'Great! Successfully send in your mail';
			 }
			 
			if($user->id){
				$response = json_encode(array('status'=>200,'message'=>'add successfully! '.$emailResponse));
			}else{
				$response = json_encode(array('status'=>403,'message'=>'Something wrong! '.$emailResponse));
			}
			
			return $response;
			
		}
		
    }
	public function acceptInvite(Request $request){
		if($request->token){
			$getUser = User::where('token', $request->token)->first();
			
			$user = User::updateOrCreate(
                                [
                                    'email' => $getUser['email'],
                                ],
                                [
                                    'user_name' => $request->user_name,
                                    'password' => bcrypt($request->password)
								]);
				
			if($user->user_name){
				$response = json_encode(array('status'=>200,'message'=>'Updated successfully! '));
			}else{
				$response = json_encode(array('status'=>403,'message'=>'Something wrong! '));
			}
			
			return $response;	
			
		}
	}
	public function updateProfile(UserRegisterRequest $request){
		if($request->id){
			$user = User::where('id', $request->id)->first();
			
			$user->name = $request->name ?? $user->name;
			$user->user_name = $request->user_name ?? $user->user_name;
			if (!empty($request['avatar']))
            {
                $path = Storage::disk('s3')->put('images/profile', $request['avatar']);
                $image_path = Storage::disk('s3')->url($path);
                $user->avatar = $image_path;
            }
			$user->email = $request->email ?? $user->email;
			$user->user_role  = $request->user_role  ?? $user->user_role ;
			$user->save();
					
			if($user->user_name){
				$response = json_encode(array('status'=>200,'message'=>'Updated successfully! '));
			}else{
				$response = json_encode(array('status'=>403,'message'=>'Something wrong! '));
			}
			
			return $response;	
			
		}
	}
}
