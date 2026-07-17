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
    	
    	$usuarios = User::orderBy('active', 'desc')->orderBy('name', 'asc')->get();
    	
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
		$usuario->active=	$request->active;
		$usuario->permitir_interacoes = $request->has('permitir_interacoes') ? 1 : 0;
		$usuario->permitir_acesso_servicos = $request->has('permitir_acesso_servicos') ? 1 : 0;

        // Tratar upload/remoção do avatar
        if ($request->has('remover_avatar') && $request->remover_avatar == 1) {
            if ($usuario->avatar) {
                $oldPath = public_path('uploads/avatares/' . $usuario->avatar);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $usuario->avatar = null;
            }
        } elseif ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($usuario->avatar) {
                $oldPath = public_path('uploads/avatares/' . $usuario->avatar);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $name = uniqid(date('HisYmd'));
            $extension = $request->avatar->getClientOriginalExtension();
            $nameFile = "{$name}.{$extension}";

            $dir = public_path('uploads/avatares');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $request->avatar->move($dir, $nameFile);
            $usuario->avatar = $nameFile;
        }

    	
	    $usuario->acesso_empresa()->sync($request->empresas_user_access);
	    $usuario->acesso_unidade	()->sync($request->unidades_user_access);

	    $usuario->save();

        $this->saveUserDepartments($usuario->id, $request->departamentos);

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
			$usuario->active=	$request->active;
			$usuario->permitir_interacoes = $request->has('permitir_interacoes') ? 1 : 0;
			$usuario->permitir_acesso_servicos = $request->has('permitir_acesso_servicos') ? 1 : 0;
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

            $this->saveUserDepartments($usuario->id, $request->departamentos);

			return redirect()->route('usuarios.index');


    }

    public function delete($id)
    {
        $user = User::destroy($id);
    

        return route('usuarios.index');
	}
	


	public function usersList()
	{
		$users = User::where('active', 1)->get();

		foreach($users as $u)
		{

			$u->name = "@".$u->name." ";
		}

		return json_encode($users);
	}

    protected function saveUserDepartments($userId, array $departments = null)
    {
        $path = storage_path('app/user_departments.json');
        
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $depts = [];
        if (file_exists($path)) {
            $depts = json_decode(file_get_contents($path), true) ?: [];
        }
        
        if (!empty($departments)) {
            $depts[$userId] = $departments;
        } else {
            if (isset($depts[$userId])) {
                unset($depts[$userId]);
            }
        }
        
        file_put_contents($path, json_encode($depts, JSON_PRETTY_PRINT));
    }

    public function toggleInteracoes($id)
    {
        $user = User::findOrFail($id);
        $user->permitir_interacoes = !$user->permitir_interacoes;
        $user->save();

        return response()->json([
            'success' => true,
            'permitir_interacoes' => $user->permitir_interacoes
        ]);
    }

    public function toggleAcessoServicos($id)
    {
        $user = User::findOrFail($id);
        $user->permitir_acesso_servicos = !$user->permitir_acesso_servicos;
        $user->save();

        return response()->json([
            'success' => true,
            'permitir_acesso_servicos' => $user->permitir_acesso_servicos
        ]);
    }
}
