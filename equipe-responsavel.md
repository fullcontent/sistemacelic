# Plano de Implementação - Equipe Responsável Dinâmica (Issue #418 - Revisado)

Este arquivo define o planejamento detalhado das tarefas necessárias para implementar a gestão de equipes dinâmica nos serviços do Sistema Celic.

## Overview
Substituir o layout de colunas estáticas no formulário de serviços por um campo dinâmico de equipe ("ir adicionando"), permitindo escolher o Cargo e o Usuário para cada linha.

- **Project Type**: WEB (Laravel MVC)
- **Primary Agent**: `backend-specialist` + `frontend-specialist`

---

## Success Criteria
- [ ] O formulário de criação/edição de serviços permite adicionar e remover linhas de membros da equipe dinamicamente.
- [ ] Cada linha de equipe permite selecionar o Cargo (Coordenador, Resp. Técnico, Analista) e o Usuário.
- [ ] O backend sincroniza os membros na tabela pivot `servico_equipe`, desvinculando quem foi removido (definindo `ativo = false` e `data_desvinculo = agora`) e vinculando novos membros.
- [ ] As colunas legadas de `servicos` (`responsavel_id`, `coresponsavel_id`, etc.) são preenchidas de forma automática para compatibilidade.
- [ ] O dashboard do usuário exibe os serviços baseando-se na pivot de equipe.

---

## Tech Stack
- **Framework**: Laravel 7+ (PHP 7.4)
- **Database**: MySQL (tabela `servico_equipe`)
- **Frontend**: Blade, Bootstrap, jQuery (para clonagem e remoção dinâmica de campos)

---

## File Structure Changes
```plaintext
app/
  └── Http/Controllers/
       ├── [MODIFY] ServicosController.php
resources/views/admin/
  ├── [MODIFY] partials/form-servico.blade.php
  └── [MODIFY] detalhe-servico.blade.php
```

---

## Task Breakdown

### Phase 1: Backend Logic Refactoring
- **Task 1.1: Atualizar ServicosController (Lógica Dinâmica)**
  - **Agent**: `backend-specialist`
  - **Skills**: `clean-code`
  - **Dependencies**: Nenhuma (migrations já criadas)
  - **Input**: `ServicosController.php`
  - **Output**: Lógica de store/update atualizada para receber arrays `equipe_cargo[]` e `equipe_user_id[]`, sincronizar na pivot salvando datas de vínculo/desvínculo, e preencher colunas legadas automaticamente.
  - **Verify**: Cadastro manual de serviço e update salvam as coleções de membros com sucesso.

### Phase 2: UI/UX (Views Blade)
- **Task 2.1: Implementar o Formulário Dinâmico**
  - **Agent**: `frontend-specialist`
  - **Skills**: `frontend-design`
  - **Dependencies**: Task 1.1
  - **Input**: `partials/form-servico.blade.php`
  - **Output**: Interface estática de equipe removida e substituída por interface dinâmica com botão "Adicionar Membro", clonando os campos de cargo e usuário via jQuery.
  - **Verify**: Ao clicar em Adicionar/Remover no browser, as linhas de Cargo + Usuário são manipuladas perfeitamente.

- **Task 2.2: Atualizar Detalhe do Serviço**
  - **Agent**: `frontend-specialist`
  - **Skills**: `frontend-design`
  - **Dependencies**: Task 2.1
  - **Input**: `detalhe-servico.blade.php`
  - **Output**: Exibição dos membros ativos e do histórico de transferências sincronizados.
  - **Verify**: Os dados salvos dinamicamente aparecem formatados.

---

## Phase X: Final Verification
- [ ] Rodar os testes automatizados do projeto: `vendor/bin/phpunit tests/Feature/ServicosEquipeTest.php`.
- [ ] Rodar o checklist do sistema: `python .agent/scripts/checklist.py .`.
