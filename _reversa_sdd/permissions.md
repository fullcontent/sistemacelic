# Matriz de Permissões (RBAC/ACL) — SistemaCelic2

O sistema utiliza um modelo híbrido de permissões, combinando papéis fixos (`privileges`) com um controle de acesso granular baseado em instâncias de dados (`user_accesses`).

## 1. Papéis de Usuário

| Papel | Descrição |
| :--- | :--- |
| **Admin** | Acesso total à área administrativa, configurações globais, gestão de faturamento e usuários. |
| **Cliente** | Acesso limitado ao Portal do Cliente. Visualiza apenas empresas/unidades vinculadas. |

## 2. Matriz de Acesso por Funcionalidade

| Funcionalidade | Admin | Cliente |
| :--- | :---: | :---: |
| Dashboard Geral / Métricas | ✅ | ❌ |
| Gestão de Usuários e Acessos | ✅ | ❌ |
| Cadastro de Empresas e Unidades | ✅ | ❌ |
| Criação de Propostas | ✅ | ❌ |
| Processamento de Faturamento | ✅ | ❌ |
| Gestão de Reembolso (ZIP) | ✅ | ❌ |
| Cadastro de Prestadores e OS | ✅ | ❌ |
| Visualizar Próprios Serviços | ✅ | ✅ |
| Adicionar Interações (Comentários) | ✅ | ✅ |
| Visualizar Taxas e Pendências | ✅ | ✅ |
| Menção de Usuários (@user) | ✅ | ✅ |

## 3. Controle de Acesso Granular (ACL)

Independente do papel, a visibilidade dos dados é filtrada pela tabela `user_accesses`.

- **Admin:** Geralmente possui acesso a todas as empresas, mas o sistema permite restringir um admin a unidades específicas se necessário.
- **Cliente:** **OBRIGATORIAMENTE** filtrado. Se um cliente não possui entrada em `user_accesses` para uma Empresa X, ele não verá nenhum serviço, taxa ou faturamento relacionado a essa empresa.

### Gatilho de Segurança
No `ClienteController`, todos os métodos de listagem utilizam `getServicosCliente()`, que executa a seguinte lógica:
1. Busca todas as `empresas` vinculadas ao `Auth::id()` via `UserAccess`.
2. Busca todas as `unidades` vinculadas ao `Auth::id()` via `UserAccess`.
3. Retorna apenas registros onde `empresa_id` ou `unidade_id` estejam nessas listas.

## 4. Responsabilidades Hierárquicas (Operacional)
Dentro de um Serviço, existem 4 níveis de "posse" que dão permissão de edição ao usuário admin:
1. **Responsável**
2. **Coresponsável**
3. **Analista 1**
4. **Analista 2**

## Escala de Confiança
- **Papéis Primários:** 🟢 CONFIRMADO
- **Lógica de Filtro ACL:** 🟢 CONFIRMADO
- **Papéis de Analista:** 🟢 CONFIRMADO
