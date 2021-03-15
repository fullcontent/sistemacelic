<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserAccess;
use App\Models\Empresa;
use App\Models\Unidade;

use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    //

    public function __construct()
    {
        
        $this->middleware('admin');

    }
    
    public function index()
    {
    	
    	$usuarios = User::all();
    	
    	return view('admin.lista-usuarios')->with('usuarios',$usuarios);

    }

    public function cadastro()
    {	
    	$empresas = Empresa::pluck('nomeFantasia','id');
    	$unidades = Unidade::pluck('nomeFantasia','id');



    	return view('admin.cadastro-usuario')->with(['empresas'=>$empresas,'unidades'=>$unidades]);
    }

    public function editar($id)
    {	
    	$usuario = User::with('empresas','unidades')->find($id);
    	$empresas = Empresa::pluck('nomeFantasia','id');
    	$unidades = Unidade::pluck('nomeFantasia','id');
    	
    	$access = UserAccess::with('empresa','unidade')->where('user_id',$id)->get();

    	
		
		return view('admin.editar-usuario')
    	->with([
    		'usuario'=>$usuario,
    		'empresas'=>$empresas,
    		'unidades'=>$unidades,
    		'user_access'=>$access,
    	]);
    }

    public function update(Request $request)
    {

    	
    	$usuario = User::find($request->id);

    	
    	if($request->password!=null)
    	{
    		$usuario->password = Hash::make($request->password);
    	}

    	
    	$usuario->name 		=	$request->name;
    	$usuario->email 	=	$request->email;
      	$usuario->privileges=	$request->privileges;

    	
	    $usuario->acesso_empresa()->sync($request->empresas_user_access);
	    $usuario->acesso_unidade	()->sync($request->unidades_user_access);

	    $usuario->save();


	    
    	return redirect()->route('usuarios.index');
                    
    }


    public function store(Request $request)
    {	
    	   	
    	   	$validator = Validator::make($request->all(), [

                'name'=>'required',
                
                'email' => 'required|email',
                'password'=>'required',
                

            ])->validate();

			$usuario = new User;

			if($request->password!=null)
			{
			$usuario->password = Hash::make($request->password);
			}

			$usuario->name 		=	$request->name;
			$usuario->email 	=	$request->email;
			$usuario->privileges=	$request->privileges;
			$usuario->save();
			
			$empresas_user_access	= $request->empresas_user_access;
			$unidades_user_access	= $request->unidades_user_access;
			
			if($empresas_user_access){
				foreach ($empresas_user_access as $e)
				{
					if(!UserAccess::where('empresa_id','=',$e)->first())
					{
						$empresas_access = new UserAccess;
						$empresas_access->user_id = $usuario->id;
						$empresas_access->empresa_id = $e;
						$empresas_access->save();
					}
				}
				}
			if($unidades_user_access){
				foreach ($unidades_user_access as $u)
				{
				//Check if not exists
					if(!UserAccess::where('unidade_id','=',$u)->first())
					{
						$unidade_access = new UserAccess;
						$unidade_access->user_id = $usuario->id;
						$unidade_access->unidade_id = $u;
						$unidade_access->save();
					}
				}
			}

			return redirect()->route('usuarios.index');


    }

    public function delete($id)
    {
        $user = User::destroy($id);
    

        return route('usuarios.index');
	}
	


	public function usersList()
	{
		$users = User::all();

		foreach($users as $u)
		{

			$u->name = "@".$u->name." ";
		}

		return json_encode($users);
	}
}
