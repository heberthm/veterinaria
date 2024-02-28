<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasRoles;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    //use HasRoles;


    public function index()
    {


    }  

    public function registration()
    {

        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request, [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'clave' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(Request $request)
    {

        $role = $request['rol'];

        return User::create([

            'name' => $request['nombre'],

            'email' => $request['email'],

           'rol' => $request['rol'],

            'password' => Hash::make($request['clave']),

        ])->assignRole($role);
    }


    public function showRegistrationForm()

    {

        $roles = User::all('id', 'rol');

        return view("register", compact("roles"));
    }




    public function edit($id)
    {

        $id_usuario  = User::find($id);

        return response()->json($id_usuario);
    }


    public function update(Request $request, $id)
    {

        $id = $request->input('id_usuario');

        $user = User::find($id);

        $user->name  = $request->nombre;

        $user->email = $request->email;

        $user->rol = $request->rol;
        
        $user->password = Hash::make($request->clave);

        $user->save();

        return response()->json(['success' => 'update successfully.']);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();

        return response()->json(['success' => 'deleted successfully.']);
    }
}
