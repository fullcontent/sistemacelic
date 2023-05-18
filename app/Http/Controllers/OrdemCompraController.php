<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestador;
use App\Models\Servico;
use App\Models\OrdemCompra;
use App\Models\OrdemCompraPagamento;
use App\Models\OrdemCompraVinculo;

use Carbon\Carbon;
use Auth;

class OrdemCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ordensCompra = OrdemCompra::all();

        return view('admin.ordemCompra.lista-ordemCompras')->with('ordensCompra',$ordensCompra);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {   

        $servico = Servico::find($id);

        $prestadores = Prestador::pluck('nome','id')->toArray();

        return view('admin.ordemCompra.cadastro-ordemCompra')->with([
            'prestadores'=>$prestadores,
            'servico'=>$servico,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   


       
        // dd($request->all());

        // An empty array is created to hold the installment details.
        $parcela = [];
        
        // The loop iterates over the valorParcela array obtained from the request.
        foreach ($request->valorParcela as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['valorParcela'] = $p;
        }
        
        // The loop iterates over the dataVencimento array obtained from the request.
        foreach ($request->dataVencimento as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['dataVencimento'] = $p;
        }
        
        // The loop iterates over the dataPagamento array obtained from the request.
        foreach ($request->dataPagamento as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['dataPagamento'] = $p;
        }
        
        // Checks if comprovante exists in the request object. If it does, then the loop iterates over the comprovante array obtained from the request.
        
        foreach ($request->comprovante as $v => $p) {
                // The value at a particular index is added as a key-value pair to the parcela array.
                $parcela[$v]['comprovante'] = $p;
        }
        
        
        // The loop iterates over the obs array obtained from the request.
        foreach ($request->obs as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['obs'] = $p;
        }
        

        if($request->servicoVinculado_reembolso)
        {
            // An empty array is created to hold the linked services.
            $servicosVinculados = [];
                    
            // The loop iterates over the servicoVinculado_id array obtained from the request.
            foreach ($request->servicoVinculado_id as $v => $p) {
                // If the value at a particular index is not false, then it is added as a key-value pair to the servicosVinculados array.
                if ($p) {
                    $servicosVinculados[$v]['servicoVinculado_id'] = $p;
                }
            }

            // The loop iterates over the servicoVinculado_id array obtained from the request.
            foreach ($request->servicoVinculado_nome as $v => $p) {
                // If the value at a particular index is not false, then it is added as a key-value pair to the servicosVinculados array.
                if ($p) {
                    $servicosVinculados[$v]['servicoVinculado_nome'] = $p;
                }
            }

            // The loop iterates over the servicoVinculado_valor array obtained from the request.
            foreach ($request->servicoVinculado_valor as $v => $p) {
                // If the value at a particular index is not false, then it is added as a key-value pair to the servicosVinculados array.
                if ($p) {
                    $servicosVinculados[$v]['servicoVinculado_valor'] = $p;
                }
            }

            // The loop iterates over the servicoVinculado_valor array obtained from the request.
            foreach ($request->servicoVinculado_reembolso as $v => $p) {
                // If the value at a particular index is not false, then it is added as a key-value pair to the servicosVinculados array.
                if ($p) {
                    $servicosVinculados[$v]['servicoVinculado_reembolso'] = $p;
                }
            }
        }
        
        
        
                
        $ordemCompra = new OrdemCompra;
        $ordemCompra->user_id = Auth::id();
        $ordemCompra->prestador_id = $request->prestador_id;
        $ordemCompra->valorServico = $request->valorServico;
        $ordemCompra->escopo = $request->escopo;
        $ordemCompra->servico_id = $request->servico_id;
        $ordemCompra->formaPagamento = $request->formaPagamento;
        $ordemCompra->situacao = $request->situacao;
        $ordemCompra->save();


        $ordemCompraServicoPrincipal = new OrdemCompraVinculo;
        $ordemCompraServicoPrincipal->ordemCompra_id = $ordemCompra->id;
        $ordemCompraServicoPrincipal->servico_id = $request->servicoPrincipal_id;
        $ordemCompraServicoPrincipal->valor = $request->servicoPrincipal_valor;
        $ordemCompraServicoPrincipal->reembolso = $request->servicoPrincipal_reembolso;
        $ordemCompraServicoPrincipal->save();


        

        foreach($servicosVinculados as $s => $ser)
        {   

            
            $ordemCompraServicoVinculado = new OrdemCompraVinculo;
            $ordemCompraServicoVinculado->ordemCompra_id = $ordemCompra->id;
            $ordemCompraServicoVinculado->servico_id = $request->servicoPrincipal_id; //Alterar para servico ID vindo do campo hidden
            $ordemCompraServicoVinculado->valor = $ser['servicoVinculado_valor'];
            $ordemCompraServicoVinculado->reembolso = $ser['servicoVinculado_reembolso'];
            $ordemCompraServicoVinculado->save();
        }




        
        // Loop through each element in the $parcela array and assign its values to a new OrdemCompraPagamento object
        foreach($parcela as $p => $par) 
        {
        
            // Create a new instance of the OrdemCompraPagamento model
            $ordemCompraPagamento = new OrdemCompraPagamento; 
        
            // Assign values to its properties based on data received
            $ordemCompraPagamento->ordemCompra_id = $ordemCompra->id; // ID of the related OrdemCompra (parent object)
            $ordemCompraPagamento->formaPagamento = $ordemCompra->formaPagamento; // Payment form selected for the OrdemCompra
            $ordemCompraPagamento->parcela = $p+1; // Number of the payment installment being processed
            $ordemCompraPagamento->valor = $par['valorParcela']; // Value of the current payment installment
            
            // If there's a "dataVencimento" value set, use the Carbon library to convert it to a valid date format and set it as the value of the "dataVencimento" property
            if($par['dataVencimento']) 
            {
                $ordemCompraPagamento->dataVencimento = Carbon::createFromFormat('d/m/Y', $par['dataVencimento'])->toDateString(); 
            }
            
            // If there's a "dataPagamento" value set, use the Carbon library to convert it to a valid date format and set it as the value of the "dataPagamento" property
            if($par['dataPagamento'])
            {
                $ordemCompraPagamento->dataPagamento = Carbon::createFromFormat('d/m/Y', $par['dataPagamento'])->toDateString();
            }
            
            // Set the value of the "obs" property to the one received for the current installment
            $ordemCompraPagamento->obs = $par['obs'];
        
            // Set the value of the "comprovante" property to the one received for the current installment
            
           
            $ordemCompraPagamento->comprovante = $par['comprovante'];

           //Se informou o arquivo, retorna um boolean
           if ($ordemCompraPagamento->comprovante->isValid()) {
               $nameFile = null;
               $name = uniqid(date('HisYmd'));
               $extension = $ordemCompraPagamento->comprovante->extension();
               $nameFile = "{$name}.{$extension}";
               //Faz o upload:
               $upload = $ordemCompraPagamento->comprovante->storeAs('comprovantes', $nameFile);
               //Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao
           
               $ordemCompraPagamento->comprovante = $upload;
           }
           

        
            // If a payment has been made (there's a comprovante file attached), set the value of the "situacao" property to 'pago'. Otherwise, set it to 'aberto'
            if($ordemCompraPagamento->comprovante)
            {
                $ordemCompraPagamento->situacao = 'pago';
            }
            else
            {
                $ordemCompraPagamento->situacao = 'aberto';
            }
            
            // Save the current OrdemCompraPagamento object to the database

            // dump($ordemCompraPagamento);

            $ordemCompraPagamento->save();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        

        $ordemCompra = OrdemCompra::find($id);
        $servico = Servico::find($ordemCompra->servico_id);
        $prestadores = Prestador::pluck('nome','id')->toArray();

        return view('admin.ordemCompra.editar-ordemCompra')->with([
            'ordemCompra'=>$ordemCompra,
            'servico'=>$servico,
            'prestadores'=>$prestadores
        ]);


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
        return $request->all();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
