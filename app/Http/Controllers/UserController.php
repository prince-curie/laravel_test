<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {
    /**
   * Returns the rules for validating a user signup credentials
   * 
   * @return Array
   */  

  private function createUserRules () {
    return [
      'password' => 'required',
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required|email:rfc,filter|unique:users'
    ];
  }

  /**
   * Returns the rules for validating a user login credentials
   * 
   * @return Array
   */
  private function loginRules () {
    return [
      'password' => 'required',
      'email' => 'required|email:rfc,filter'
    ];
  }

  /**
   * Creates a new User
   * 
   * @param object Request
   * 
   * @return response
   */

  public function save (Request $request) {
    $this->validate($request, $this->createUserRules());

    $newUser = User::create ([
      'first_name' => $request->input ('first_name'), 
      'last_name' => $request->input ('last_name'), 
      'email' => $request->input ('email'), 
      'password' => Hash::make ($request->input ('password'))
    ]);

    return response ()->json ([
      'status' => 'Success', 
      'message' => 'User signup successful'
    ], 201);
  }

    /**
   * Logs in a User
   * 
   * @param object Request
   * 
   * @return response
   */
  public function login (Request $request) {
    $this->validate($request, $this->loginRules());
    $input = $request->only(['email', 'password']);
  
    $token = Auth::attempt($input);
    if (!$token) {
      return response()->json([
        "status" => "Fail", 
        "message" => "Unauthorized",
      ], 401);  
    }
    
    return response()->json([
      "status" => "Success", 
      "message" => "Login successful",
      "result" => [
        "token" => $token,
        "expires_in" => Auth::factory()->getTTL() * 60,
      ],
    ], 200);  
  }

    /**
   * Fetches a user profile
   * 
   * @return response
   */
  public function profile () {
    $user = Auth::user();

    return response()->json([
      "status" => "Success", 
      "message" => "Profile fetch successful",
      "result" => $user,
    ], 200);
  }
}
