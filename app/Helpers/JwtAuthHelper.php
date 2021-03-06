<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use App\User;

use Illuminate\Support\Facades\Log;

class JwtAuthHelper{

    //Llave secreta para codificar los token
    public $key;

    public function __construct(){
        //Asignamos una clave secreta
        $this->key = 'asdasdqwdqwasdqw¡?$%%&asdasd1123123';
    }

    public function signup($email,$password,$getToken=null){

        //Buscamos el user en la base de datos
        $user = User::where([
                    'email'=>$email,
                    'password'=>$password
                ])->first();

        if($user){
            //Devuelvo Token

            //Agregamos en el token toda la informacion relevante
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(), //tiempo de creacion
                'exp' => time() + (7 * 24 * 60) //tiempo de expiracion, 1 semana para el ejemplo
            );

            //Codificaremos el token usando nuestra clave secreta y especificar algoritmo
            $jwt = JWT::encode($token,$this->key,'HS256');

            //Decodificaremos el token
            $decoded = JWT::decode($jwt,$this->key,array('HS256'));

            if(is_null($getToken)){
                return $jwt;
            }else{
                return $decoded;
            }

        }else{
            //Devuelvo Error
            return array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Login ha fallado'
            );
        }

    }

    public function checkToken($jwt, $getIdentity = false){
        Log::info("JwtAuthHelper.checkToken:\nToken recibido: ".$jwt);
        $auth = false;

        try {
            $decoded = JWT::decode($jwt,$this->key,array('HS256'));
        }catch(\Firebase\JWT\ExpiredException $e){
            $auth = false;
            Log::info("JwtAuthHelper.checkToken: ExpiredException:\n".$e);
        }catch(\DomainException $e){
            Log::info("JwtAuthHelper.checkToken: DomainException:\n".$e);
            $auth = false;
        }catch(\UnexpectedValueException $e){
            Log::info("JwtAuthHelper.checkToken: UnexpectedValueException:\n".$e);
            $auth = false;
       }

        if(isset($decoded) && $decoded && $decoded->sub){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }

}
