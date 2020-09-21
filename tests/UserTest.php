<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * A basic test example.
     *
     * @return void
     */

    private function newUser () { 
      return [
        "email" => "merryhello@chin.com",
        "first_name" => "me",
        "last_name" => "she",
        "password" => "ytjtgjr543"
      ];
    }

    public function testExample()
    {
      $this->get('/');

      $this->assertEquals(
          $this->app->version(), $this->response->getContent()
      );
    }

    public function testCreateUserSuccessfully () {
      $response = $this->post('api/user', $this->newUser())
        ->seeJsonEquals([
          'status' => 'Success', 
          'message' => 'User signup successful'
        ]);

      $this->assertEquals(201, $this->response->status());
    
      $this->seeInDatabase('users', ['email' => "merryhello@chin.com"]);
    }

    public function testCreateUserWithInvalidEmail () {
      $newUser = [
        "email" => "merryhello@chin",
        "first_name" => "me",
        "last_name" => "she",
        "password" => "ytjtgjr543"
      ];

      $response = $this->post('api/user', $newUser)
        ->seeJsonEquals([
          "email" => [
              "The email must be a valid email address."
          ]
        ]);

      $this->assertEquals(422, $this->response->status());
    }

    public function testLoginSuccessfully () {
      $this->post('api/user', $this->newUser());
      $newUserLoginDetails = [
        "email" => "merryhello@chin.com",
        "password" => "ytjtgjr543"
      ];

      $response = $this->post('api/login', $newUserLoginDetails)
        ->seeJson([
          "status" => "Success",
          "message" => "Login successful",]
        );

      $this->assertEquals(200, $this->response->status());
    }

    public function testLoginFail () {
      $this->post('api/user', $this->newUser());
      $newUserLoginDetails = [
        "email" => "merryhello.com",
        "password" => "ytjtgjr543"
      ];

      $response = $this->post('api/login', $newUserLoginDetails)
        ->seeJsonEquals([
        "email" => [
            "The email must be a valid email address."
        ]
      ]);;
      
      $this->assertEquals(422, $this->response->status());
    }

    public function testProfileFail () {
      $response = $this->get('api/profile');

      $this->assertEquals(401, $this->response->status());

      $this->assertEquals(
        'Unauthorized.', $this->response->getContent()
      );
    }

    public function testProfileSuccess () {
      $this->post('api/user', $this->newUser());
      $newUserLoginDetails = [
        "email" => "merryhello@chin.com",
        "password" => "ytjtgjr543"
      ];

      $response = $this->post('api/login', $newUserLoginDetails);

      $token = $response->response['result']['token'];

      $profileResponse = $this->get('api/profile', ['Authorization' => "Bearer $token"])
        ->seeJson([
          "status" => "Success",
          "message" => "Profile fetch successful",
        ]);

      $this->assertEquals(200, $this->response->status());
    }

}
