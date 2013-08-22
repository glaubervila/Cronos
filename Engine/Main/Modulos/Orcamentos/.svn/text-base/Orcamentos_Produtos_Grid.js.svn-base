/**
 * @package     : Orcamentos
 * @name        : Orcamentos_Produtos_Grid
 * @Diretorio   : Main/Modulos/Orcamentos/
 * @Dependencias:
 * @date        : 25/04/2011
 */

var Orcamentos_Produtos_Grid = Ext.extend(Ext.grid.GridPanel,{

    $depends     : [
        '../Produtos/Produtos_Localizar_Window'
    ]

    , border     : false
    , stripeRows : true
    , loadMask   : true
    , id         : 'Orcamentos_Produtos_Grid'

    , main_url   : 'main.php'
    , main_class : 'Orcamentos_Servidor'
    , pk_id      : 'pk_orcamento_produto'
    , metodo_load  : 'getOrcamentoProdutos_entregue'
    , metodo_delete: 'delete_orcamento_produtos_entregue'
    , metodo_submit: ''

    , IdRegistro   : 0

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }

    , disableSelection:false


    , constructor: function() {
        Orcamentos_Produtos_Grid.superclass.constructor.apply(this,arguments);

        this.addEvents({
            alterou_quantidade  : true
            , alterar_observacao: true
            , excluiu_item      :true
            , incluir_produto   : true
        });
    }


    ,initComponent: function() {

        //store do grid
        this.store = new Ext.data.JsonStore({
            url              : this.main_url
            , root           : 'rows'
            , idProperty     : this.pk_id
            , totalProperty  : 'totalCount'
            //, autoLoad       : true
            //, autoDestroy    : true
            //, clicksToEdit   : 2
            , autoSave       : false
            , baseParams     : {
                classe     : this.main_class
                , action   : this.metodo_load
                //, limit    : 30
                , pk_orcamento: this.IdRegistro
            }
            , fields:[
                {name:this.pk_id         , type:'string'}
                , {name: 'fk_orcamento'  , type:'string'}
                , {name: 'fk_id_produto' , type:'int'}
                , {name: 'descricao_curta', type:'string'}
                , {name: 'descricao_longa', type:'string'}
                , {name: 'quantidade'    , type:'float'}
                , {name: 'preco'         , type:'float'}
                , {name: 'valor_total'   , type:'float'}
                , {name: 'observacao_produto', type: 'string'}
                , {name: 'action_observacao', type: 'string'}
                , {name: 'qtip_observacao', type: 'string'}
            ]
        });


        // Create RowActions Plugin
        this.action = new Ext.ux.grid.RowActions({
            header:''
            //,autoWidth:false
            //,hideMode:'display'
            ,keepSelection:true
            ,actions:[{
                iconCls:'silk-delete'
                ,tooltip:'Click Para Excluir este Registro'
            },{
                iconCls:'silk-bricks'
                ,tooltip:'Click Para Alterar este Registro'
            }]
            , callbacks:{
                'icon-plus':function(grid, record, action, row, col) {
                    Ext.ux.Toast.msg('Callback: icon-plus', 'You have clicked row: <b>{0}</b>, action: <b>{0}</b>', row, action);
                }
            }
        });

        this.action.on({
            action:function(grid, record, action, row, col) {
                //Ext.ux.Toast.msg('Event: action', 'You have clicked row: <b>{0}</b>, action: <b>{1}</b>', row, action);
                switch (action) {
                    // Botao Excluir
                    case 'silk-delete':
                        grid.action_excluir(grid, row, record);
                    break;
                    // Botao Editar
                    case 'silk-bricks':
                        grid.onGridRowDblClick(grid, row, record);
                    break;
                }
            }
            , beforeaction:function() {
                //Ext.ux.Toast.msg('Event: beforeaction', 'You can cancel the action by returning false from this event handler.');
            }
        });


        //demais atributos do grid
        Ext.apply(this,{
            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , deferEmptyText : false
                , getRowClass: function(record) {
                    // Formatando a linha pelo valor do campo quantidade
                    var qtd = record.data.quantidade;
                    if (qtd == 0){
                        return 'row-red'
                    }
                }
            }
            , columnLines: true
            , cls: 'custom-grid'
            ,tbar: [{
                text   : 'Incluir Produto'
                , id   : 'btn_incluir_produto'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this.onClickBtnIncluirProduto
                , tooltip: {title:'Incluir Produtos',text:'Click para adicionar um novo produto'}
            }]
            , columns:[
            new Ext.grid.RowNumberer()
            , this.action
            ,{
                dataIndex   : this.pk_id
                , hidden    : true
            },{
                dataIndex   : 'fk_id_produto'
                , header    : 'C&oacute;digo'
                , sortable  : true
                , width     :80
            },{
                dataIndex   : 'descricao_longa'
                , header    : 'Descri&ccedil;&atilde;o'
                , sortable  : true
                , width     :320
            },{
                dataIndex   : 'quantidade'
                , header    : 'Quantidade'
                , sortable  : true
                , width     :80
            },{
                dataIndex   : 'preco'
                , header    : 'Valor Unit&aacute;rio'
                , renderer  : 'brMoney'
                , sortable  : true
                , width     :120
            },{
                dataIndex   : 'valor_total'
                , header    : 'Valor Total'
                , renderer  : 'brMoney'
                , sortable  : true
                , width     :120
            }]
            , plugins:[this.action]
        })

        Orcamentos_Produtos_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Orcamentos_Produtos_Grid.superclass.initEvents.call(this);

        this.on({
            scope        : this
            , rowdblclick: this.onGridRowDblClick
        });
    }
    //Overrides

    // liberar as referencias feitas quando uma interface e destruida
    , onDestroy: function() {
        Orcamentos_Produtos_Grid.superclass.onDestroy.apply(this,arguments);
        //destroi a janela
    }

    // Demais Metodos
    , onGridRowDblClick: function(grid, rowIndex, e){
        var record = grid.getStore().getAt(rowIndex);
        Ext.Msg.prompt('Quantidade', 'Digite a Nova Quantidade:', function(btn, qtd){
            if (btn == 'ok'){
                this.alterar_quantidade(record, qtd);
            }
        },this);
    }

    , alterar_quantidade:function(record, qtd){

        var PkOrcamento = record.get('fk_orcamento');
        var registroID  = record.get(this.pk_id);
        var produto     = record.get('fk_id_produto');

        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Orcamentos_Servidor'
                , action: 'adicionar_produto'
                , 'fk_orcamento' :PkOrcamento
                , 'pk_orcamento_produto': registroID
                , 'quantidade_venda':qtd
                , 'pk_id_produto' : produto
                , 'alteracao_qtd' : true
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){

                    Ext.getCmp('main_statusbar').msg('save');
                    this.fireEvent('alterou_quantidade', this, response);
                }
                else {
                    // COLOCAR MENSAGEM DE ERRO, FALHOU AO ALTERAR QUANTIDADE
                }
            }
        });

    }

    , action_excluir: function(grid, row, record){

        // Confimar
        Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo excluir o(s) registro(s) selecionado(s)?',function(opt){
            if(opt === 'no') {
                return;
            }
            else {
//                 orcamento = record.data.fk_orcamento;
//                 produto = record.data.pk_orcamento_produto;
                this.alterar_quantidade(record, 0);
                //grid.excluir_registro(produto, orcamento);

            }
        },this);
    }

    , onClickBtnIncluirProduto: function(){
        console.log('Orcamentos_Produtos_Grid - onClickBtnIncluirProduto');

        this.criaWindowPesquisa();
        this.window_pesquisa_produtos.show();
    }

    , criaWindowPesquisa: function(){
        console.log('Orcamentos_Produtos_Grid - criaWindowPesquisa');
        if(!this.window_pesquisa_produtos) {
            this.window_pesquisa_produtos = new Produtos_Localizar_Window({
                listeners  :{
                    scope  : this
                    , seleciona_produto : this.onSelecionaProduto
                }
            })
        }
        return this.window_pesquisa_produtos
    }

    , onSelecionaProduto:function(obj, pk_id_produto){
        console.log('Orcamentos_Produtos_Grid - onSelecionaProduto('+pk_id_produto+')');
        //alert(registroId);
        this.window_pesquisa_produtos.hide();

        this.escolhe_quantidade(pk_id_produto);
    }

    , escolhe_quantidade:function(pk_id_produto){

        Ext.MessageBox.prompt('Quantidade', 'Digite a Quantidade:', function(btn,value){
            console.log(btn);
            console.log(value);
            if(value > 0){
                //Ext.MessageBox.hide();
                this.insere_produto(pk_id_produto, value);
            }
            else{
                this.onClickBtnIncluirProduto();
                //this.escolhe_quantidade(pk_id_produto);
            }
       }, this);
    }

    , insere_produto:function(pk_id_produto, quantidade){
        console.log('Orcamentos_Produtos_Grid - insere_produto('+pk_id_produto+','+quantidade+')');
        //alert('Registro: ' + registroId + 'Catalogo: '+pk_catalogo);

        Ext.Ajax.request({
            url    : this.main_url
            , params : {
                classe    : 'Orcamentos_Servidor'
                , action  : 'adicionar_produto'
                , fk_orcamento  : this.IdRegistro
                , pk_id_produto: pk_id_produto
                , quantidade_venda: quantidade
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.getCmp('main_statusbar').msg('ok');

                    Ext.ux.Toast.msg('Sucesso!', response.msg);

                    this.store.reload();

                    this.fireEvent('incluir_produto', this, response);
                }
                else {
                    Ext.getCmp('main_statusbar').msg('error');
                    Ext.MessageBox.show({ title:'Desculpe!', msg: response.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
                }
            }
        });
    }


//     // Listener disparado ao clicar no botao "Excluir Selecionados"
//     , onBtnExcluirSelecionadosClick: function() {
//         //busco selecionados
//         var arrSelecionados = this.getSelectionModel().getSelections();
// 
//         if( arrSelecionados.length === 0 ) {
//             Ext.Msg.alert('Aten&ccedil;&atilde;o','Selecione ao menos um registro!')
//             return false;
//         }
//         else {
//             Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo excluir o(s) registro(s) selecionado(s)?',function(opt){
// 
//                 if(opt === 'no') {
//                     return;
//                 }
//                 else {
//                     var ids = [];
//                     for( var i = 0 ; i < arrSelecionados.length ; i++ ) {
//                         ids.push( arrSelecionados[i].get(this.pk_id) );
//                     }
// 
//                     // Saber qual o codigo do orcamento e passar o parametro
//                     //this.excluir_registro(ids, fk_orcamento);
//                 }
//             },this);
//         }
//     }
// 
//     , action_excluir: function(grid, row, record){
// 
//         // Confimar
//         Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo excluir o(s) registro(s) selecionado(s)?',function(opt){
//             if(opt === 'no') {
//                 return;
//             }
//             else {
//                 orcamento = record.data.fk_orcamento;
//                 produto = record.data.pk_orcamento_produto;
// 
//                 grid.excluir_registro(produto, orcamento);
// 
//             }
//         },this);
//     }
// 
//     // Neste Caso e necessario passar o codigo do orcamento
//     , excluir_registro: function(ids, fk_orcamento){
// 
//         this.el.mask('Excluindo Registros');
//         Ext.getCmp('main_statusbar').msg('job');
// 
//         Ext.Ajax.request({
//             url    : this.main_url
//             , params : {
//                 classe : this.main_class
//                 , action: this.metodo_delete
//                 , 'pk_orcamento_produto[]': ids
//                 , 'fk_orcamento':orcamento
//             }
//             ,scope  : this
//             ,success: function() {
//                 this.el.unmask();
//                 this.store.removeAll();
//                 this.store.reload();
//                 Ext.getCmp('main_statusbar').msg('ok');
// 
//                 Ext.ux.Toast.msg('Exclusão de Item', 'Item Excluido com Sucesso');
//                 // Dispara Evento
//                 this.fireEvent('excluiu_item', this);
//             }
//         });
//     }

    , action_observacao: function(grid, row, record){
        // Janela de Observacoes
        this.win_observacao = null;
        this.win_observacao = grid.cria_win_observacao();

        // Setando a chave primaria e atribuindo o record
        this.win_observacao.setRegistroID(record.data.pk_orcamento_produto);
        this.win_observacao.setRecord(record);

        // Exibindo a Janela
        this.win_observacao.show();
    }


    , cria_win_observacao: function(){

        if (!this.win_observacao){
            this.win_observacao = new WinObservacoesProdutos({
                listeners  :{
                    scope  : this
                    , alterar_observacao : this.onAlterarObservacao
                }
            });

        }
        return this.win_observacao;
    }

    , onAlterarObservacao: function(){
        this.store.reload();
    }
});
Ext.reg('e-Orcamentos_Produtos_Grid', Orcamentos_Produtos_Grid);

// ----------------------------------------------//---------------------------------------------- //

var WinObservacoesProdutos = Ext.extend(Ext.Window,{

    id: 'Win_Observacoes_Produtos'

    , main_url   : 'main.php'
    , main_class : 'Orcamentos_Servidor'

    , pk_id      : 'pk_orcamento_produto'
    , metodo_load  : ''
    , metodo_delete: ''
    , metodo_submit: 'adicionar_observacao_produto'

    , modal  : true
    , constrain: true
    , maximizable: false
    , resizable:false

    , width  : 300
    , height : 200
    , title  : 'Cadastro de Observa&ccedil;&otilde;es Por Produtos'
    , layout : 'fit'
    , autoScroll : false

    , IdRegistro: 0
    , record: null

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }

    , setRecord:function(record){
        this.record = record;
    }

    , constructor: function() {
        this.addEvents({
            alterar_observacao: true
        });

        WinObservacoesProdutos.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {

        this.formPanel = new Ext.form.FormPanel({
             bodyStyle: 'padding:5px;'
            , border         : false
            , labelAlign     : 'top'
            , items:[{
                xtype:'fieldset'
                , anchor:'99%'
                , autoScroll : false
                , border     : false
                , items: [{
                    fieldLabel   : 'Observação'
                    , id         : 'txtObservacaoProduto'
                    , name       : 'observacao_produto'
                    , xtype      : 'textarea'
                    , width      : 250
                    , height     : 100
                    , maxLength  : 130
                }]
            }]
        });

        Ext.apply(this,{
            items  : this.formPanel
            , focus: function(){
                // Campo Quantidade Setando Foco
                Ext.get('txtObservacaoProduto').focus();
            }
            , bbar : ['->'
            , this.btnSalvar = new Ext.Button({
                text     : 'Salvar'
                , iconCls: 'silk-disk'
                , scope  : this
                , handler: this.onBtnSalvarClick
            })
            , this.btnExcluir = new Ext.Button({
                text     : 'Apagar'
                , iconCls: 'silk-delete'
                , scope  : this
                , handler: this.onBtnApagarClick
            })
            ]
        })

       WinObservacoesProdutos.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        WinObservacoesProdutos.superclass.initEvents.call(this);
    }

    , show: function() {
        WinObservacoesProdutos.superclass.show.apply(this,arguments);

        if(this.IdRegistro !== 0) {
            // Atribuindo valor ao campo
            Ext.getCmp('txtObservacaoProduto').setValue(this.record.data.observacao_produto);
        }
        else {
            this.formPanel.getForm().reset();
        }
    }

    , onBtnSalvarClick: function(){
        var form = this.formPanel.getForm();

        if(!form.isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        else {
            this.formSubmit();
        }
    }

    , onBtnApagarClick: function(){
        // Apagando o formulario
        this.formPanel.getForm().reset();
        // Enviando Formulario
        this.formSubmit();

    }

    , formSubmit: function(){
        var form = this.formPanel.getForm();

        this.el.mask('Salvando informa&ccedil;&otilde;es');
        Ext.getCmp('main_statusbar').msg('saving');

        form.submit({
            url: this.main_url
            , params: {
                classe       : this.main_class
                , action     : this.metodo_submit
                , 'pk_orcamento_produto' : this.IdRegistro
            }
            , scope:this
            , success: function(form, action) {
                var json = Ext.decode(action.response.responseText);
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('save');

                Ext.ux.Toast.msg('Sucesso!', 'Registro Salvo Com Sucesso!');

                // Disparando Evendo alterar_observacao
                this.fireEvent('alterar_observacao',this, json);

                this.close();
            }
            , failure: function(form , action){
                var obj = Ext.decode(action.response.responseText);

                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('error');

                Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });

                Ext.ux.Toast.msg('Falha', 'Falha ao Gravar o Registro!');
                this.close();
            }
        });
    }


});
Ext.reg('e-WinObservacoesProdutos', WinObservacoesProdutos);

// ---------------------------------------------//-------------------------------------------------

var Orcamentos_Produtos_Observacoes_Grid = Ext.extend(Ext.grid.GridPanel,{

    border     : false
    , stripeRows : true
    , loadMask   : true
    , id         : 'Orcamentos_Produtos_Observacoes_Grid'

    , main_url   : 'main.php'
    , main_class : 'Orcamentos_Servidor'
    , pk_id      : 'pk_orcamento_produto'
    , metodo_load  : 'getOrcamentoProdutosObservacoes'
    , metodo_delete: 'delete_orcamento_produtos'
    , metodo_submit: ''

    , IdRegistro   : 0
    , PkOrcamento  : 0

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }
    , setPkOrcamento: function(PkOrcamento) {
        this.PkOrcamento = PkOrcamento;
    }
    , disableSelection:false


    , constructor: function() {
        Orcamentos_Produtos_Observacoes_Grid.superclass.constructor.apply(this,arguments);

        this.addEvents({
            alterar_observacao: true
        });
    }


    ,initComponent: function() {

        //store do grid
        this.store = new Ext.data.JsonStore({
            url              : this.main_url
            , root           : 'rows'
            , idProperty     : this.pk_id
            , totalProperty  : 'totalCount'
            //, autoLoad       : true
            , autoDestroy    : true
            //, clicksToEdit   : 2
            , autoSave       : false
            , baseParams     : {
                classe     : this.main_class
                , action   : this.metodo_load
                //, limit    : 30
                , pk_orcamento: this.IdRegistro
            }
            , fields:[
                {name:this.pk_id         , type:'string'}
                , {name: 'fk_orcamento'  , type:'string'}
                , {name: 'fk_id_produto' , type:'int'}
                , {name: 'descricao_curta', type:'string'}
                , {name: 'observacao_produto', type: 'string'}
                , {name: 'action_observacao', type: 'string'}
                , {name: 'qtip_observacao', type: 'string'}
            ]
        });


        // Create RowActions Plugin
        this.action = new Ext.ux.grid.RowActions({
            header:''
            //,autoWidth:false
            //,hideMode:'display'
            ,keepSelection:true
            ,actions:[{
                iconIndex:'action_observacao'
                , qtipIndex:'qtip_observacao'
                //, iconCls:'silk-comments-edit'
                , tooltip:'Click Para Incluir uma Observa&ccedil;&atilde;o a este Registro'
            }
            ]
            , callbacks:{
                'icon-plus':function(grid, record, action, row, col) {
                    Ext.ux.Toast.msg('Callback: icon-plus', 'You have clicked row: <b>{0}</b>, action: <b>{0}</b>', row, action);
                }
            }
        });

        this.action.on({
            action:function(grid, record, action, row, col) {
                //Ext.ux.Toast.msg('Event: action', 'You have clicked row: <b>{0}</b>, action: <b>{1}</b>', row, action);
                switch (action) {
                    // Botao Observacao
                    case 'silk-comments':
                        grid.action_observacao(grid, row, record);
                    break;
                    case 'silk-comment-edit':
                        grid.action_observacao(grid, row, record);
                    break;

                }
            }
            , beforeaction:function() {
                //Ext.ux.Toast.msg('Event: beforeaction', 'You can cancel the action by returning false from this event handler.');
            }
        });


        //demais atributos do grid
        Ext.apply(this,{
            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , columns:[
            new Ext.grid.RowNumberer()
            , this.action
            ,{
                dataIndex   : this.pk_id
                , hidden    : true
            },{
                dataIndex   : 'fk_id_produto'
                , header    : 'C&oacute;digo'
                , sortable  : true
                , width     : 80
            },{
                dataIndex   : 'descricao_curta'
                , header    : 'Descri&ccedil;&atilde;o'
                , sortable  : true
                , width     : 250
            },{
                dataIndex   : 'observacao_produto'
                , header    : 'Observa&ccedil;&otilde;es'
                , sortable  : true
                , width     : 400
            }]
            , plugins:[this.action]
        })

        Orcamentos_Produtos_Observacoes_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Orcamentos_Produtos_Observacoes_Grid.superclass.initEvents.call(this);

        this.on({
            scope        : this
            , rowdblclick: this.onGridRowDblClick
        });
    }
    //Overrides

    // liberar as referencias feitas quando uma interface e destruida
    , onDestroy: function() {
        Orcamentos_Produtos_Observacoes_Grid.superclass.onDestroy.apply(this,arguments);
        //destroi a janela
    }

    // Demais Metodos
    , onGridRowDblClick: function(grid, rowIndex, e){
        var record = grid.getStore().getAt(rowIndex);
        this.action_observacao(grid,rowIndex,record);
    }

    , action_observacao: function(grid, row, record){
        // Janela de Observacoes
        this.win_observacao = null;
        this.win_observacao = grid.cria_win_observacao();

        // Setando a chave primaria e atribuindo o record
        this.win_observacao.setRegistroID(record.data.pk_orcamento_produto);
        this.win_observacao.setRecord(record);

        // Exibindo a Janela
        this.win_observacao.show();
    }


   , cria_win_observacao: function(){

        if (!this.win_observacao){
            this.win_observacao = new WinObservacoesProdutos({
                listeners  :{
                    scope  : this
                    , alterar_observacao : this.onAlterarObservacao
                }
            });

        }
        return this.win_observacao;
    }

    , onAlterarObservacao: function(){
        this.store.reload();
        this.fireEvent('alterar_observacao',this);
    }
});
Ext.reg('e-Orcamentos_Produtos_Observacoes_Grid', Orcamentos_Produtos_Observacoes_Grid);