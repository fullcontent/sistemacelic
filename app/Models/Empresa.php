<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{


    	public function unidades()
		{
			return $this->hasMany('App\Models\Unidade');
		}


		public function users()
		{
			return $this->hasManyThrough('App\User','App\UserAccess','empresa_id','id');
		}

		
		public function taxas()
		{
			return $this->hasManyThrough('App\Models\Taxa','App\Models\Servico','empresa_id','servico_id','id');
		}

		public function arquivos()
		{
			return $this->hasMany('App\Models\Arquivo');
		}

		public function servicosFaturar()
		{
			return $this->hasManyThrough('App\Models\Servico','App\Models\Unidade','empresa_id');
		}

		public function servicos()
		{
			return $this->hasManyThrough('App\Models\Servico','App\Models\Unidade','empresa_id');
		}

		public function propostas()
		{
			return $this->hasManyThrough('App\Models\Servico','App\Models\Unidade','empresa_id','proposta');
		}



}
