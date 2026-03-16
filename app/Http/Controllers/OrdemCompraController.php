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
     * @return \Illuminate\View\View
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
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create($id)
    {   

        $servico = Servico::with(['unidade', 'empresa'])->find($id);

        if (!$servico) {
            return redirect()->back()->with('error', 'Serviço não encontrado.');
        }

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
        if($request->comprovante)
        {
            foreach ($request->comprovante as $v => $p) {
                // The value at a particular index is added as a key-value pair to the parcela array.
                $parcela[$v]['comprovante'] = $p;
        }
        }
        
        
        
        // The loop iterates over the obs array obtained from the request.
        foreach ($request->obs as $v => $p) {
            // The value at a particular index is added as a key-value pair to the parcela array.
            $parcela[$v]['obs'] = $p;
        }
        

        $servicosVinculados = [];

        if($request->has('servicoVinculado_id') && is_array($request->servicoVinculado_id))
        {
            // The loop iterates over the servicoVinculado_id array obtained from the request.
            foreach ($request->servicoVinculado_id as $v => $p) {
                if ($p) {
                    $servicosVinculados[$v]['servicoVinculado_id'] = $p;
                    $servicosVinculados[$v]['servicoVinculado_nome'] = $request->servicoVinculado_nome[$v] ?? '---';
                    $servicosVinculados[$v]['servicoVinculado_valor'] = $request->servicoVinculado_valor[$v] ?? 0;
                    $servicosVinculados[$v]['servicoVinculado_reembolso'] = $request->servicoVinculado_reembolso[$v] ?? 'nao';
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
            $ordemCompraServicoVinculado->servico_id = $ser['servicoVinculado_id'];
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
            
           
            if(isset($par['comprovante']))
            {
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



        return redirect()->route('servicos.show',$request->servico_id)->with('message','Ordem de compra criada com sucesso!');



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
        

        $ordemCompra = OrdemCompra::with(['pagamentos', 'vinculos.servico', 'prestador'])->find($id);
        
        if (!$ordemCompra) {
            return redirect()->back()->with('error', 'Ordem de compra não encontrada.');
        }

        $servico = Servico::with(['unidade', 'empresa'])->find($ordemCompra->servico_id);
        $prestadores = Prestador::pluck('nome','id')->toArray();

        // Encontrar o vínculo que corresponde ao serviço principal
        $vinculoPrincipal = $ordemCompra->vinculos->where('servico_id', $ordemCompra->servico_id)->first();
        $vinculoOutros = $ordemCompra->vinculos->where('servico_id', '!=', $ordemCompra->servico_id);

        return view('admin.ordemCompra.editar-ordemCompra')->with([
            'ordemCompra'=>$ordemCompra,
            'servico'=>$servico,
            'prestadores'=>$prestadores,
            'vinculoPrincipal'=>$vinculoPrincipal,
            'vinculoOutros'=>$vinculoOutros
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
        $ordemCompra = OrdemCompra::find($id);
        
        if (!$ordemCompra) {
            return redirect()->back()->with('error', 'Ordem de compra não encontrada.');
        }

        $ordemCompra->user_id = Auth::id();
        $ordemCompra->prestador_id = $request->prestador_id;
        $ordemCompra->valorServico = $request->valorServico;
        $ordemCompra->escopo = $request->escopo;
        $ordemCompra->formaPagamento = $request->formaPagamento;
        $ordemCompra->situacao = $request->situacao ?? $ordemCompra->situacao;
        $ordemCompra->save();

        // Atualizar vínculos
        OrdemCompraVinculo::where('ordemCompra_id', $id)->delete();

        // Principal
        $ordemCompraServicoPrincipal = new OrdemCompraVinculo;
        $ordemCompraServicoPrincipal->ordemCompra_id = $id;
        $ordemCompraServicoPrincipal->servico_id = $request->servicoPrincipal_id;
        $ordemCompraServicoPrincipal->valor = $request->servicoPrincipal_valor;
        $ordemCompraServicoPrincipal->reembolso = $request->servicoPrincipal_reembolso;
        $ordemCompraServicoPrincipal->save();

        // Outros
        if($request->has('servicoVinculado_id') && is_array($request->servicoVinculado_id))
        {
            foreach ($request->servicoVinculado_id as $v => $p) {
                if ($p) {
                    $ocv = new OrdemCompraVinculo;
                    $ocv->ordemCompra_id = $id;
                    $ocv->servico_id = $p;
                    $ocv->valor = $request->servicoVinculado_valor[$v] ?? 0;
                    $ocv->reembolso = $request->servicoVinculado_reembolso[$v] ?? 'nao';
                    $ocv->save();
                }
            }
        }

        // Atualizar pagamentos (simplificado: remove e recria se mudar, ou apenas permite atualizar se for o mesmo número de parcelas)
        // Para manter a simplicidade e consistência com o store:
        OrdemCompraPagamento::where('ordemCompra_id', $id)->delete();
        
        if($request->valorParcela) {
            foreach($request->valorParcela as $p => $vParcela) {
                $pag = new OrdemCompraPagamento;
                $pag->ordemCompra_id = $id;
                $pag->formaPagamento = $ordemCompra->formaPagamento;
                $pag->parcela = $p + 1;
                $pag->valor = $vParcela;
                
                if(isset($request->dataVencimento[$p]) && $request->dataVencimento[$p]) {
                    $pag->dataVencimento = Carbon::createFromFormat('d/m/Y', $request->dataVencimento[$p])->toDateString();
                }
                
                if(isset($request->dataPagamento[$p]) && $request->dataPagamento[$p]) {
                    $pag->dataPagamento = Carbon::createFromFormat('d/m/Y', $request->dataPagamento[$p])->toDateString();
                }
                
                $pag->obs = $request->obs[$p] ?? null;

                // Lógica de Comprovante
                if($request->hasFile("comprovante.$p"))
                {
                    $file = $request->file("comprovante.$p");
                    if ($file->isValid()) {
                        $name = uniqid(date('HisYmd'));
                        $extension = $file->extension();
                        $nameFile = "{$name}.{$extension}";
                        $upload = $file->storeAs('comprovantes', $nameFile);
                        $pag->comprovante = $upload;
                    }
                }
                elseif(isset($request->comprovante_atual[$p]))
                {
                    // Mantém o arquivo antigo se não subiu um novo
                    $pag->comprovante = $request->comprovante_atual[$p];
                }

                $pag->situacao = $pag->comprovante ? 'pago' : 'aberto';
                $pag->save();
            }
        }

        return redirect()->route('servicos.show', $ordemCompra->servico_id)->with('message', 'Ordem de compra atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ordemCompra = OrdemCompra::find($id);
        
        if (!$ordemCompra) {
            return redirect()->back()->with('error', 'Ordem de compra não encontrada.');
        }

        $servico_id = $ordemCompra->servico_id;

        // Deletar dependências manually se não tiver cascade no DB
        OrdemCompraPagamento::where('ordemCompra_id', $id)->delete();
        OrdemCompraVinculo::where('ordemCompra_id', $id)->delete();
        
        $ordemCompra->delete();

        return redirect()->route('servicos.show', $servico_id)->with('message', 'Ordem de compra removida com sucesso!');
    }
}
