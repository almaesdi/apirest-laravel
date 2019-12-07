<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuthHelper;
use App\Car;

use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(Request $request){
        $hash = $request->header('Authorization',null);

        $jwtAuthHelper = new JwtAuthHelper();
        $checkToken = $jwtAuthHelper->checkToken($hash);

        if($checkToken){
            echo "Usuario Autenticado";
        }else{
            echo "Usuario NO authenticado";
        }

        die();
    }

    public function store(Request $request){
        $hash = $request->header('Authorization',null);

        $jwtAuthHelper = new JwtAuthHelper();
        $checkToken = $jwtAuthHelper->checkToken($hash);

        if($checkToken){
            //Guardaremos el auto

            //Recojemos datos por POST
            $json = $request->input('json',null);
            $params = json_decode($json);

            //Obtenemos los datos del user del token
            $user = $jwtAuthHelper->checkToken($hash,true);

            //Validacion
            /*Para validar peticiones RESTFULL se debe usar VALIDATOR
            Para formularios normales de Laravel podemos usar Validate*/
            $validate = Validator::make(json_decode($json,true),[
                'title' => 'required',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);


            if($validate->fails()){
                return response()->json($validate->errors(),400);
            }

            //Creo el auto
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => '200'
            );

        }else{
            $data = array(
                'message' => 'Login Incorrecto',
                'status' => 'error',
                'code' => '300'
            );
        }

        return response()->json($data,200);
    }
}
