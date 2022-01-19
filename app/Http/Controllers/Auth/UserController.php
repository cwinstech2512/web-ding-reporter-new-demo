<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


class UserController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     *
     */
    public function roles() {
      $qry = "SELECT * FROM roles";
      $roles = DB::select($qry);
      return response()->json([
          'roles' => $roles,
          'code' => '200',
          'msg' => ""
      ]);
    }

    /**
     *
     * 100; unsupported method
     * 101: invalid param
     * 102: invalid user
     * 103: db exception
     * @param  \Illuminate\Http\Request $request
     * @param {$id} from URL
     */
    public function account(Request $request, $id = null)
    {
      $method = strtolower($request->method());
      
      if ($method == "get") { // get user list
        $qry = "SELECT U.id, U.email, U.name, R.role_id as role FROM users as U JOIN role_users as R WHERE U.id = R.user_id";
        if ($id != null) {
          $qry = $qry . " AND U.id = " . $id;
        }
        $users = DB::select($qry);
        if (Count($users) == 0 && $id != null) {
          return '{"code": 102, "msg": "invalid user"}';
        }
        return response()->json([
            'users' => $users,
            'code' => '200',
            'msg' => ""
        ]);
      }

      if ($method == "put") { // update user
        if ($id == null) {
          return '{"code": 101, "msg": "invalid parameters"}';
        }
        $user = DB::select("SELECT * FROM users WHERE id = '$id'");
        if (Count((array)$user) == 0) {
          return '{"code": 102, "msg": "invalid user"}';
        }
        $role = null;
        $pwd = null;
        $data = Array($request->All()['params'])[0];
        if (array_key_exists('role', $data)) {
          $role = intval($data['role']);
          $cnt =  Count((array)DB::select("SELECT * FROM roles WHERE id = $role"));
          if ($cnt == 0) {
            return '{"code": 101, "msg": "invalid parameters 2"}';
          }
        }
        if (array_key_exists('password', $data)) {
          $pwd = Hash::make($data['password']);
        }

        if ($pwd == null && $role == null) {
          return '{"code": 101, "msg": "invalid parameters 3"}';
        }
        try {
          DB::beginTransaction();
          if ($pwd != null) {
            DB::update('UPDATE users SET password = "'.$pwd.'" WHERE id = '.$id.'');
          }
          if ($role != null) {
            DB::update('UPDATE role_users SET role_id = '.$role.' WHERE user_id = '.$id );
          }
          DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
            return '{"code": 103, "msg": "' . $e->getMessage() . '"}';
        }
        return '{"code": 200, "msg": ""}';
      }

      if ($method == "post") { // create user
        $data = Array($request->All()['params'])[0];
        if ( !array_key_exists('role',     $data) ||
             !array_key_exists('name',     $data) ||
             !array_key_exists('password', $data) ){
          return '{"code": 101, "msg": "invalid parameters"}';
        }

        $role = intval($data['role']);
        $cnt =  Count((array)DB::select("SELECT * FROM roles WHERE id = ".$role));
        if ($cnt == 0) {
          return '{"code": 101, "msg": "invalid parameters 2"}';
        }
        $name = $data['name'];
        $pwd = Hash::make($data['password']);
        $email = $name."@ehsnchat.com.tw";
        $user = DB::select("SELECT * FROM users WHERE email = '$email'");
        $cnt = Count((array)$user);
        if ($cnt != 0) {
          return '{"code": 102, "msg": "invalid user"}';
        }

        try {
            // $time  = explode(" ", microtime());
            // $time  = $time[1] . ($time[0] * 1000);
            // $time2 = explode(".", $time);
            // $time  = $time2[0];

            // $token = str_replace('-', '', Uuid::uuid5(Uuid::NAMESPACE_DNS, $email.$time)->toString());

            DB::beginTransaction();
            $uid = DB::table('users')->insertGetId(
              array(
                'name' => $name,
                'email' => $email,
                // 'token' => $token,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'password' => $pwd));
            DB::insert("INSERT INTO role_users (role_id, user_id) values (?, ?)",[$role, $uid]);
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
            return '{"code": 103, "msg": "' . $e->getMessage() . '"}';
        }
        return '{"code": 200, "msg": ""}';
      }
      // others
      return '{"code": 100, "msg": "unsupported method"}';
    }
}
