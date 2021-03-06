<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User; 
use Illuminate\Support\Facades\Auth; 

use Validator;

class UserController extends Controller 
{

	public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $success['status'] = false;
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) 
        {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['status'] = true;
            return response()->json(['success' => $success, 'user'=>$user], $this->successStatus);
        } else {
            return response()->json(['success' => $success]);
        }    
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $success['status'] = false;
        $validator = Validator::make(
            $request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => $success], 401);
        }
        $group = DB::table('group')->where('group_name', 'user')->get();
        $user = User::create([ 
            'group_id' => $group[0]->group_id,
            'name' => $request->name,
            'last_name' => ' ', 
            'email' => $request->email, 
            'password' => bcrypt($request->password)
        ]);
        $success['status'] = true;
        return response()->json(['success'=>$success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }
}