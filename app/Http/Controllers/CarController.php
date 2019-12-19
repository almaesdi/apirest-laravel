<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuthHelper;
use App\Car;

use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(){
        $cars = Car::All()->load('user'); //Traera todos los autos y el <User></User>

        return response()->json(array(
                'cars' => $cars,
                'status' => 'success',
            )
        );
    }

    public function show($id){
        $car = Car::find($id)->load('user'); //Traera todos los autos y el <User></User>

        return response()->json(array(
                'car' => $car,
                'status' => 'success',
            )
        );
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

    public function update($id, Request $request){
        $hash = $request->header('Authorization',null);

        $jwtAuthHelper = new JwtAuthHelper();
        $checkToken = $jwtAuthHelper->checkToken($hash);

        if($checkToken){
            //Actualizaremos el auto

            //Recojemos datos por POST
            $json = $request->input('json',null);
            $params = json_decode($json);

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


            $car = Car::where('id',$id)->update(json_decode($json,true)); //Traera todos los autos y el <User></User>

            $data = array(
                'car' => $params,
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

    public function destroy($id, Request $request){
        $hash = $request->header('Authorization',null);

        $jwtAuthHelper = new JwtAuthHelper();
        $checkToken = $jwtAuthHelper->checkToken($hash);

        if($checkToken){
            //Eliminaremos el auto

            $car = Car::find($id);

            $car->delete();

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
