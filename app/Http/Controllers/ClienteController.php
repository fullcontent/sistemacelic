<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    //

    public function index()
    {
    	return view ('user.dashboard');
    }

    public function empresas()
    {
    	return "empresas";
    }

    public function unidades()
    {
    	return "unidades";
    }

    public function servicos()
    {
    	return "servicos";
    }
}
