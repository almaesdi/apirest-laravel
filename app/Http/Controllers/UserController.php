<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Helpers\JwtAuthHelper;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function register(Request $request){
        //header('Access-Control-Allow-Origin: *');
        //header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        //header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        //header("Allow: GET, POST, OPTIONS, PUT, DELETE");

        //Recojemos las variables request, debiese ser un JSON
        $json = $request->input('json',null);
        $params = json_decode($json);

        $role = "User_Role";
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        //Compruebo que las variables obligatorias esten seteadas
        if(!is_null($email) && !is_null($name) && !is_null($password)){

            //Crear Usuario
            $user = new User();
            $user->role = $role;
            $user->name = $name;
            $user->surname = $surname;
            $user->email = $email;
            $user->password= hash('sha256',$password);

            if (User::where('email', $email)->first()){
                //Usuario No creado
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Usuario No creado'
                );
            }else{
                $user->save();
                //Usuario Creado
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Usuario creado'
                );
            }


        }else{
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Usuario No creado, Datos Obligatorios Null'
            );
        }

        return response()->json($data,200);
    }

    public function login(Request $request){
        $jwtAuthHelper = new JwtAuthHelper();

        //Recojemos las variables request, debiese ser un JSON
        $json = $request->input('json',null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;

        //Cifrar la password
        $password = hash('sha256',$password);

        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $signup = $jwtAuthHelper->signup($email,$password);

        }elseif($getToken){
            $signup = $jwtAuthHelper->signup($email,$password,$getToken);

        }else{
            $signup = array(
                'status'=>'error',
                'code'=>'400',
                'message'=>'Login fail'
            );
        }

        return response()->json($signup,200);
    }
}
