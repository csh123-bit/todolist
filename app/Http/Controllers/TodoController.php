<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Exception;
use Illuminate\Http\Request;
use illuminate\support\facades\Auth;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos=Auth::user()->todos;
        return response()->json(["status"=>"success","error"=>false, "count"=>count($todos),"data"=>$todos],200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "title"=>"required|min:3|unique:todos,title",
            "description"=>"required"
        ]);
        if($validator->fails()){
            return $this->validationErrors($validator->errors());
        }

        try {
            $todo = Todo::create([
                "title"=>$request->title,
                "description"=>$request->description,
                "user_id"=>Auth::user()->id
            ]);
            return response()->json(["status"=>"success","error"=>false,"message"=>"success todo crated"],201);
        } catch (Exception $exception) {
            return response()->json(["status"=>"failed","error"=>$exception->getMessage()],404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = Auth::user()->todos->find($id);

        if($todo){
            return response()->json(["status"=>"success","error"=>false,"data"=>$todo],200);
        }
        return response()->json(["status"=>"failed","error"=>true, "message"=>"Failed no todo found"],404);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $todo=Auth::user()->todos->find($id);

        if($todo){
            $validator=Validator::make($request->all(),[
                'title'=>'required',
                'description'=>'required'
            ]);

            if($validator->fails()){
                return $this->validationErrors($validator->errors());
            }

            $todo['title']=$request->title;
            $todo['description']=$request->description;

            if($request->active){
                $todo['active']=$request->active;
            }

            if($request->completed){
                $todo['completed']=$request->completed;
            }

            $todo->save();
            return response()->json(["status"=>"success","error"=>false,"message"=>"success, todo updated"],201);
        }
        return response()->json(["status"=>"failed","error"=>true, "message"=>"failed no todo found"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo = Auth::user()->todos->find($id);
        if($todo){
            $todo->delete();
            return response()->json(["status"=>"success","error"=>false,"message"=>"success todo deleted"]);
        }
        return response()->json(["status"=>"failed","error"=>true,"message"=>"Failed no todo found"],404);
    }
}
