/**
 * @package     : Orcamentos
 * @name        : Orcamentos_Servidor_Form
 * @Diretorio   : Main/Modulos/Orcamentos/
  * @date       : 11/01/2011
 * @version     : 1.0
 */
var Orcamentos_Servidor_Form = Ext.extend(Ext.Window,{

    $depends     : [
        'Orcamentos_Produtos_Grid.js'
        ,'Orcamentos_Produtos_Lista.js'
        ,'Orcamentos_CmbStatus.js'
        , '../Clientes/Clientes_Localizar_Window'
    ]
    , id: 'Orcamentos_Servidor_Form'

    //, identificacao : '5006' // Identificacao para permissoes
    , closeAction: 'hide'
    , constrain  : true

    , width      : 900
    , height     : 420
    , title      : 'Pedidos'
    , layout     : 'fit'
    , autoScroll : false
    , border     : false

    , main_url   : 'main.php'
    , main_class : 'Orcamentos_Servidor'
    , pk_id      : ''
    , metodo_load  : 'getOrcamentoById'
    , metodo_delete: 'cancelar_orcamento'
    , metodo_submit: 'CriaOrcamento'
    , metodo_alterar_observacoes: 'adicionar_observacao_orcamento'

    , IdRegistro   : 0
    , IdCliente    : 0
    , registro     : ''

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }
    , setRegistro: function(registro) {
        this.registro = registro;
    }
    , setClienteId: function(IdCliente) {
        this.IdCliente = IdCliente;
    }
    , constructor: function() {

        Orcamentos_Servidor_Form.superclass.constructor.apply(this,arguments);

        this.addEvents({
            alterar_status: true
            , alterar_observacao:true
            , novo_orcamento: true
        });

    }

    , initComponent: function() {

        this.orcamentos_produtos_grid = new Orcamentos_Produtos_Grid({
            id: 'orcamentos_produtos_grid'
            , title : 'Pedido'
            , bodyStyle:'padding:0px;'
            , listeners:{
                scope  : this
                , alterou_quantidade: this.onAlterarQuantidade
                , excluiu_item: this.onAlterarQuantidade
                , alterar_observacao: this.formLoad
                , incluir_produto: this.onIncluirProduto
            }
        });

        this.produtos_lista = new Orcamentos_Produtos_Lista({
            id: 'Orcamentos_Produtos_Lista'
            , title  : 'Pré Pedido'
        });

        this.observacoes_produtos_grid = new Orcamentos_Produtos_Observacoes_Grid({
            id: 'Orcamentos_Produtos_Observacoes_grid'
            , region: 'center'
            , title  : 'Observação dos Produtos'
            , bodyStyle:'padding:0px;'
            , listeners:{
                scope  : this
                , alterar_observacao: this.formLoad
            }
        });

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            id           : 'frmOrcamentos_Servidor_Form'
            , layout     : 'fit'
            , border     : false
            , autoScroll : false
            , labelAlign : 'top'
            , items:[{
                xtype:'tabpanel'
                , id: 'form_orcamentos_tabpanel'
                , activeTab: 0
                , border: false
                , items:[{
                    title    : 'Informa&ccedil;&otilde;es do Pedido'
                    , id: 'tab_informacoes'
                    , border :false
                    , layout:'column'
                    , items:[{
                        border : false
                        , columnWidth:0.5
                        , bodyStyle:'padding:10px'
                        , items: [{
                            xtype:'fieldset'
                            , title : 'Or&ccedil;amento'
                            //, width      : 410
                            , items: [{
                                xtype: 'textfield'
                                , name       : 'pk_orcamento'
                                , fieldLabel : 'Orcamento Nº'
                                , id       : 'txtPkOrcamento'
                                , width    : 150
                                , readOnly : true
                            },{
                                xtype        : 'e-Cmb_Status_Pedido'
                                , fieldLabel : 'Status'
                                , id         : 'cmbStatusPedido'
                                , width      : 190
                                , col        : true
                                , listeners:{
                                    scope  : this
                                    , 'select': this.onAlteraStatus
                                }
                            },{
                                fieldLabel   : 'Nome Vendedor'
                                , name       : 'nome_vendedor'
                                , xtype      : 'textfield'
                                , width      : 350
                                , readOnly : true
                            }]
                        },{
                            xtype:'fieldset'
                            , title : 'Cliente'
                            , border: true
                            , autoScroll : false
                            //, width      : 410
                            , items: [{
                                fieldLabel   : 'C&oacute;digo Cliente'
                                , id         : 'txtCodigoCliente'
                                , name       : 'fk_id_cliente'
                                , xtype      : 'textfield'
                                , col        : true
                                , readOnly : true
                            },{
                                fieldLabel   : 'Nome Cliente'
                                , id         : 'txtNomeCliente'
                                , name       : 'nome_cliente'
                                , xtype      : 'textfield'
                                , width      : 260
                                , col        : true
                                , readOnly : true
                            }]
                        }]
                    },{
                        border : false
                        , columnWidth:0.4
                        , bodyStyle:'padding:10px'
                        , items: [{
                            xtype:'fieldset'
                            , title : 'Histórico'
                            , autoScroll : false
                            , width      : 200
                            , border     : true
                            , items: [{
                                xtype: 'textfield'
                                , fieldLabel: 'Data Pedido'
                                , name     : 'dt_pedido'
                                , readOnly : true
                                , width    : 150
                            },{
                                xtype: 'textfield'
                                , fieldLabel: 'Recebimento'
                                , name     : 'dt_envio'
                                , readOnly : true
                                , width    : 150
                            },{
                                xtype: 'textfield'
                                , fieldLabel: 'Entrega'
                                , name     : 'dt_entrega'
                                , readOnly : true
                                , width    : 150
                            },{
                                xtype: 'textfield'
                                , fieldLabel: 'Finaliza&ccedil;&atilde;o'
                                , name     : 'dt_finalizacao'
                                , readOnly : true
                                , width    : 150
                            }]
                        }]
                    }]
                }
                , this.produtos_lista 
                , this.orcamentos_produtos_grid
                ,{
                    title    : 'Observa&ccedil;&otilde;es'
                    , id     : 'tab_observacoes'
                    , layout : 'border'
                    , items:[{
                        region   : 'north'
                        , title  : 'Observação do Pedido'
                        , height : 150
                        , items: [{
                            xtype:'fieldset'
                            , anchor:'99%'
                            , autoScroll : false
                            , border     : false
                            , items: [{
                                fieldLabel   : 'Observação do Pedido'
                                , id         : 'txtObservacaoPedido'
                                , name       : 'observacao'
                                , xtype      : 'textarea'
                                , width      : '100%'
                                //, height     : 100
                                , maxLength  : 500
                                , hideLabel  : true
                            }]
                        }]
                        , tbar : [
                        this.btnAlterarComentarioPedido = new Ext.Button({
                            id       : 'btnAlterarComentarioPedido'
                            , text   : 'Alterar Coment&aacute;rio'
                            , iconCls: 'silk-comment'
                            , scope  : this
                            , handler: this.onClickBtnAlterarComentarioPedido
                        })
                        , this.btnApagarComentarioPedido = new Ext.Button({
                            id       : 'btnApagarComentrioPedido'
                            , text   : 'Apagar Coment&aacute;rio'
                            , iconCls: 'silk-comment-delete'
                            , scope  : this
                            , handler: this.onClickBtnApagarComentarioPedido
                        })]

                    },this.observacoes_produtos_grid]
                }
                ,{
                    title    : 'Finaliza&ccedil;&atilde;o'
                    , id     : 'tab_finalizacao'
                    , border :false
                    , items:[{
                        border : false
                        , bodyStyle:'padding:10px'
                        , items: [{
                            xtype:'fieldset'
                            , autoScroll : false
                            , title  : 'Finaliza&ccedil;&atilde;o'
                            , width  : 200
                            , border : true
                            , items: [{
                                fieldLabel   : 'Finalizadora'
                                , name       : 'nome_finalizadora'
                                , xtype      : 'textfield'
                                , width      : 160
                                , readOnly   : true
                            },{
                                fieldLabel   : 'Parcelamento'
                                , name       : 'parcelamento'
                                , xtype      : 'textfield'
                                , width      : 160
                                , readOnly   : true
                            },{
                                fieldLabel   : 'Frete'
                                , name       : 'nome_frete_por_conta'
                                , xtype      : 'textfield'
                                , width      : 160
                                , readOnly   : true
                            },{
                                fieldLabel   : 'NFe'
                                , name       : 'nome_nfe'
                                , xtype      : 'textfield'
                                , width      : 160
                                , readOnly   : true
                            }]
                        },{
                            xtype:'fieldset'
                            , title : 'Valor Original'
                            , autoScroll : false
                            , width      : 200
                            , border     : true
                            , col : true
                            , items: [{
                                xtype      : 'masktextfield'
                                , mask       : 'R$ #9.999.990,00'
                                , money      : true
                                , readOnly   :true
                                , fieldLabel: 'Valor Total'
                                , name   : 'valor_total'
                            },{
                                xtype       : 'masktextfield'
                                , mask      : 'R$ #9.999.990,00'
                                , money     : true
                                , readOnly  : true
                                , fieldLabel: 'Desconto'
                                , name      : 'desconto'
                            },{
                                xtype       : 'masktextfield'
                                , mask      : 'R$ #9.999.990,00'
                                , money     : true
                                , readOnly  : true
                                , fieldLabel: 'Valor A Pagar'
                                , name      : 'valor_pagar'
                            }]
                        },{
                            xtype:'fieldset'
                            , title : 'Valor Pago'
                            , autoScroll : false
                            , width      : 200
                            , col : true
                            , items: [{
                                xtype      : 'masktextfield'
                                , mask       : 'R$ #9.999.990,00'
                                , money      : true
                                , readOnly   :true
                                , fieldLabel: 'Valor Total'
                                , name   : 'valor_total_entrega'
                            },{
                                xtype       : 'masktextfield'
                                , mask      : 'R$ #9.999.990,00'
                                , money     : true
                                , readOnly  : true
                                , fieldLabel: 'Desconto'
                                , name      : 'desconto_final'
                            },{
                                xtype       : 'masktextfield'
                                , mask      : 'R$ #9.999.990,00'
                                , money     : true
                                , readOnly  : true
                                , fieldLabel: 'Valor A Pagar'
                                , name      : 'valor_pago'
                            }]
                        }]
                    }]
                }]
            }]
        })



        Ext.apply(this,{
            items: [this.formPanel]
            , tbar : [
                this.btnSeparar_orcamento = new Ext.Button({
                    text   : 'Separar'
                    , id   : 'btn_separar'
                    , iconCls: 'silk-box'
                    , scope  : this
                    , handler: this.onClickBtnSepararClick
                })
                , this.btnPagar_orcamento = new Ext.Button({
                    text   : 'Pagar'
                    , id   : 'btn_enviar_pdv'
                    , iconCls: 'silk-money'
                    , scope  : this
                    , handler: this.onClickBtnEnviarPdv
                }),
                this.btnEntrega_orcamento = new Ext.Button({
                    text   : 'Entregar'
                    , id   : 'btn_entregar'
                    , iconCls: 'silk-lorry'
                    , scope  : this
                    , handler: this.onClickBtnEntregarClick
                }),
                this.btnFinaliza_orcamento = new Ext.Button({
                    text   : 'Finalizar'
                    , id   : 'btn_finalizar'
                    , iconCls: 'silk-accept'
                    , scope  : this
                    , handler: this.onClickBtnFinalizarClick
                }),
                '-',
                this.btnCancelarPedido = new Ext.Button({
                    text   : 'Cancelar'
                    , id   : 'btn_cancelar'
                    , iconCls: 'silk-cancel'
                    , scope  : this
                    , handler: this.onClickBtnCancelarClick
                }),
                '-',
                this.btnImpressao = new Ext.Button({
                    text   : 'Imprimir'
                    , iconCls: 'silk-printer'
                    , id     : 'btn_imprimir_pedido'
                    , scope  : this
                    , handler: this.onClickBtnImprimir_pedido
/*                    , menu: [{
                        text   : 'Pré Pedido'
                        , id   : 'btn_imprimir_pre_pedido'
                        , scope  : this
                        , handler: this.onClickBtnImprimirPrePedido
                    },{
                        text   : 'Pedido'
                        , id   : 'btn_imprimir_enviado'
                        , scope  : this
                        , handler: this.onClickBtnImprimirPedidoEnviado
                    }]*/
                })
                , '-'
                , {
                    xtype: 'button'
                    , iconCls: 'silk-coins'
                }
                ,{
                    xtype        : 'textfield'
                    , id         : 'txtValorTotalMain'
                    , width      : 110
                    //, height     : 40
                    //, rowspan: 2
                    , readOnly   :true
                    , value      : 'R$ 0.00'
                    , style: {
                        fontSize: '16px'
                        , fontWeight:'bold'
                        , color:'#3E6AAA'
                        , border:'none'
                        , background:'transparent'
                        //, textAlign:'right'
                    }
                }
                ,'-'
                ,{
                    xtype: 'button'
                    , iconCls: 'silk-cart'
                },{
                    xtype        : 'textfield'
                    , id         : 'txtQtdItensMain'
                    , readOnly   :true
                    , width      : 100
                    //, height     : 40
                    , value      : '0 Itens'
                    , style: {
                        fontSize: '16px'
                        , fontWeight:'bold'
                        , color:'#3E6AAA'
                        , border:'none'
                        , background:'transparent'
                        //, textAlign:'right'
                    }
                }
                ,'-'
                ,{
                    xtype: 'button'
                    , iconCls: 'silk-user'
                },{
                    xtype        : 'textfield'
                    , id         : 'txtNomeClienteMain'
                    , width      : 240
                    //, height     : 40
                    , readOnly   : true
                    , value      : ''
                    //, colspan: 3
                    , style: {
                        fontSize: '10px'
                        , fontWeight:'bold'
                        , color:'#3E6AAA'
                        , border:'none'
                        , background:'transparent'
                        //, textAlign:'right'
                    }
                }
            ]
        })

        Orcamentos_Servidor_Form.superclass.initComponent.call(this);
    }


    , show: function() {
        console.log('Orcamentos_Servidor_Form - show');
        Orcamentos_Servidor_Form.superclass.show.apply(this,arguments);

        this.formPanel.getForm().reset();
        this.orcamentos_produtos_grid.store.removeAll();
        this.observacoes_produtos_grid.store.removeAll();
        this.zerar_valores();

        if (this.IdRegistro > 0) {
            this.formLoad();
        }
        else {
            this.novoOrcamento();
        }
    }

    , formLoad:function(){
        console.log('Orcamentos_Servidor_Form - formLoad');
        // Limpando Formulario e Grid
//         this.formPanel.getForm().reset();
//         this.orcamentos_produtos_grid.store.removeAll();
//         this.observacoes_produtos_grid.store.removeAll();

        this.el.mask('Carregando informa&ccedil;&otilde;es');
        Ext.getCmp('main_statusbar').msg('load');
        // Carregando Formulario
        this.formPanel.getForm().load({
            url : this.main_url
            , params : {
                classe   : this.main_class
                , action : this.metodo_load
                , 'pk_orcamento': this.IdRegistro
            }
            , scope: this
            , success: this._onFormLoad
            , failure: this._onFormLoadFailure
        });
        // Carregando Produtos
        this.orcamentos_produtos_grid.setRegistroID(this.IdRegistro);
        this.orcamentos_produtos_grid.store.load({params:{pk_orcamento:this.IdRegistro}});

        // Carregando Observacoes
        this.observacoes_produtos_grid.setRegistroID(this.IdRegistro);
        this.observacoes_produtos_grid.store.load({params:{pk_orcamento:this.IdRegistro}});

        // Carregando Pedido Original
        this.produtos_lista.setRegistroID(this.IdRegistro);
        this.produtos_lista.store.load({params:{pk_orcamento:this.IdRegistro}});

    }

   , onDestroy: function() {

        Orcamentos_Servidor_Form.superclass.onDestroy.apply(this,arguments);
    }


    , _onFormLoadFailure: function(form, request) {

        var obj = Ext.decode(request.response.responseText);
        this.el.unmask();
        //this.hide();
        this.close();
        Ext.getCmp('main_statusbar').msg('error');
        Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg + "<br>C&oacute;d: " + obj.code, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });

        this.el.unmask();
        //this.hide();
        this.close();
    }

    , _onFormLoad: function(form, request) {
        console.log('Orcamentos_Servidor_Form - _onFormLoad');
        var data = request.result.data;

        // Setando Titulo
        var titulo = 'Pedido: '+ data.pk_orcamento + ' - ' + data.nome_cliente;
        this.setTitle(titulo);

        Ext.getCmp('txtNomeClienteMain').setValue(data.nome_cliente);

        // Tratamento pelo Status
//     0 - Aberto     [Azul]
//     1 - Fechado    [Verde]
//     2 - Cancelado  [Vermelho]
//     3 - À Separar  [Laranja]
//     4 - Separando [ Azul Claro ]
//     5 - Aguardando Pagamento [ Verde ]
//     6 - Entregue [Azul]
//     7 - Finalizado [Cinza]
        if (data.status == 0) {
            Ext.getCmp('btn_separar').enable();
            Ext.getCmp('btn_enviar_pdv').disable();
            Ext.getCmp('btn_entregar').disable();
            Ext.getCmp('btn_finalizar').disable();
            Ext.getCmp('btn_cancelar').enable();

            // Desabilita as abas tambem
            //Ext.getCmp('Orcamentos_Produtos_Observacoes_grid').enable();
            Ext.getCmp('form_orcamentos_tabpanel').hideTabStripItem('Orcamentos_Produtos_Lista');
            Ext.getCmp('orcamentos_produtos_grid').setTitle('Pré Pedido');
            Ext.getCmp('form_orcamentos_tabpanel').hideTabStripItem('Orcamentos_Produtos_Lista');
        }
        // A Separar - Habilita so a Impressao de PDF
        if (data.status == 3) {
            // desabilita tudo
            Ext.getCmp('btn_separar').enable();
            Ext.getCmp('btn_enviar_pdv').disable();
            Ext.getCmp('btn_entregar').disable();
            Ext.getCmp('btn_finalizar').disable();
            Ext.getCmp('btn_cancelar').enable();

        }
        // Separando - Habilita o Envio pro Pdv
        if (data.status == 4) {
            // Desabilita tudo
            Ext.getCmp('btn_separar').disable();
            Ext.getCmp('btn_enviar_pdv').enable();
            Ext.getCmp('btn_entregar').disable();
            Ext.getCmp('btn_finalizar').disable();
            Ext.getCmp('btn_cancelar').enable();

            // Tratamento das Abas
            Ext.getCmp('orcamentos_produtos_grid').setTitle('Pedido');
            Ext.getCmp('form_orcamentos_tabpanel').unhideTabStripItem('Orcamentos_Produtos_Lista');

        }
        // Aguardando Pagamento - Habilita a Saida para Entrega
        if (data.status == 5) {
            // Desabilita tudo
            Ext.getCmp('btn_separar').disable();
            Ext.getCmp('btn_enviar_pdv').disable();
            Ext.getCmp('btn_entregar').enable();
            Ext.getCmp('btn_finalizar').disable();
            Ext.getCmp('btn_cancelar').enable();
        }
        // Entregue - Habilita a Finalizacao do Pedido
        if (data.status == 6) {
            // Desabilita tudo
            Ext.getCmp('btn_separar').disable();
            Ext.getCmp('btn_enviar_pdv').disable();
            Ext.getCmp('btn_entregar').disable();
            Ext.getCmp('btn_finalizar').enable();
            Ext.getCmp('btn_cancelar').enable();
        }

        // Cancelado, Finalizado - desabilita tudo
        if ((data.status == 2) || (data.status == 7) ){
            Ext.getCmp('btn_separar').disable();
            Ext.getCmp('btn_enviar_pdv').disable();
            Ext.getCmp('btn_entregar').disable();
            Ext.getCmp('btn_finalizar').disable();
            Ext.getCmp('btn_cancelar').disable();
        }


        this.atualizar_valores_inicio(null,data);

        // Setando o ObjPedido na Classe
        this.setRegistro(data);
        
        // tiro a mascara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
    }

    , onClickBtnAlterarComentarioPedido: function(){
        var form = this.formPanel.getForm();

        //if(!form.isValid()) {
          //  Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            //return false;
        //}
        //else {
            this.formObservacoesSubmit();
        //}
    }

    , onClickBtnApagarComentarioPedido: function(){
        // Apagando o formulario
        Ext.getCmp('txtObservacaoPedido').setValue('');
        // Enviando Formulario
        this.formObservacoesSubmit();

    }

    , formObservacoesSubmit: function(){
        var form = this.formPanel.getForm();

        this.el.mask('Salvando informa&ccedil;&otilde;es');
        Ext.getCmp('main_statusbar').msg('saving');

        observacao = Ext.getCmp('txtObservacaoPedido').getValue();
        
        
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Orcamentos_Servidor'
                , action: 'adicionar_observacao_orcamento'
                , 'pk_orcamento' : this.IdRegistro
                , 'observacao': observacao
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    //tiro mascara
                    this.el.unmask();
                    Ext.getCmp('main_statusbar').msg('save');

                    Ext.ux.Toast.msg('Sucesso!', 'Registro Salvo Com Sucesso!');

                    // Disparando Evendo alterar_observacao
                    this.fireEvent('alterar_observacao',this, response);

                    this.formLoad();
                }
                else {

                    this.el.unmask();
                    Ext.getCmp('main_statusbar').msg('error');

                    Ext.MessageBox.show({ title:'Desculpe!', msg: response.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });

                    Ext.ux.Toast.msg('Falha', 'Falha ao Gravar o Registro!');
                    this.formLoad();
                }
            }
        });
    }

    , onAlteraStatus: function(combo){
        console.log('Orcamentos_Servidor_Form - onAlteraStatus');

        var cmb = Ext.getCmp('cmbStatusPedido');

//         status = cmb.getValue();
        var status = cmb.value;

        Ext.Ajax.timeout = 9999;
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : this.main_class
                , action: 'AlterarStatus'
                , 'pk_orcamento' : this.IdRegistro
                , 'status': status
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.ux.Toast.msg('Sucesso!', 'Altera&ccedil;&atilde;o Realizada com Sucesso!');
                    // Disparando Evendo alterar_observacao
                    this.fireEvent('alterar_status',this);
                }
                else{
                    Ext.getCmp('main_statusbar').msg('error');
                    Ext.MessageBox.show({
                        title:'Desculpe!',
                        msg: response.msg + "<br>C&oacute;d: " + response.code,
                        buttons: Ext.MessageBox.OK,
                        icon:  Ext.MessageBox.WARNING
                    });
                }
            }
        });
    }

    , onClickBtnSepararClick: function(){
        console.log('Orcamentos_Servidor_Form - onClickBtnSepararClick');
        console.log('onClickBtnSepararClick');
        // Alterar o Status do Pedido para 4 - Separando 
        var combo = Ext.getCmp('cmbStatusPedido');
        combo.setValue(4);

        this.onAlteraStatus(combo);

    }


    , onClickBtnEnviarPdv: function(){

        Ext.Ajax.timeout = 999;
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Orcamentos_Servidor'
                , action: 'Gera_Pedido_Pdv'
                , 'pk_orcamento': this.IdRegistro
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.getCmp('main_statusbar').msg('ok');
                    Ext.ux.Toast.msg('Sucesso!', 'Pedido Enviado Para o Pdv!');

                    // Alterar o Status do Pedido para 5 - Aguardando Pagamento
                    var combo = Ext.getCmp('cmbStatusPedido');
                    combo.setValue(5);
                    this.onAlteraStatus(combo);
//                     this.formLoad();
                }
                else {
                    Ext.getCmp('main_statusbar').msg('error');
                    Ext.MessageBox.show({ title:'Desculpe!', msg: response.msg , buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
                }
            }
        });
    }

    , onClickBtnEntregarClick: function(){
        // Alterar o Status do Pedido para 6 - Entregue
        var combo = Ext.getCmp('cmbStatusPedido');
        combo.setValue(6);
        this.onAlteraStatus(combo);

//         this.formLoad();
    }

    , onClickBtnFinalizarClick: function(){
        // Alterar o Status do Pedido para 7 - Entregue
        var combo = Ext.getCmp('cmbStatusPedido');
        combo.setValue(7);
        this.onAlteraStatus(combo);

//         this.formLoad();
    }

    , onClickBtnCancelarClick: function(){
        Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja Realmente Cancelar este Pedido?',function(opt){
            if(opt === 'no') {
                return;
            }
            else {

                // Alterar o Status do Pedido para 2 - Cancelado
                var combo = Ext.getCmp('cmbStatusPedido');
                combo.setValue(2);
                this.onAlteraStatus(combo);
                this.formLoad();
            }
        },this);
    }

    , onClickBtnImprimir_pedido: function(){
        console.log('onClickBtnImprimirPedido');

        // Saber o Status do Pedido
        var status = this.registro.status;
        console.log('Status '+status);

        // Sempre imprimir o Pedido Final, o pedido final ele começa como uma copia do pre pedido,
        // no momento da separacao o pedido final e igual o pre pedido, so depois que da separacao e que vai haver diferencas.
        this.onClickBtnImprimirPedidoEnviado();
    }

    , onClickBtnImprimirPrePedido: function(){
        console.log('onClickBtnImprimirPrePedido');
        Ext.Ajax.timeout = 999;
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Orcamentos_PDF'
                , action: 'Monta_Orcamento_Pdf'
                , 'pk_orcamento': this.IdRegistro
                , 'tipo_pdf': 1
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.getCmp('main_statusbar').msg('ok');

                    window.location.href = "Main/PHP/Download_Arquivo.php?file="+response.file+'&path='+response.path+'&filename='+response.filename;
                }
                else {
                    alert('failure');
                }
            }
        });

    }

    , onClickBtnImprimirPedidoEnviado: function(){

        Ext.Ajax.timeout = 999;
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Orcamentos_PDF'
                , action: 'Monta_Orcamento_Pdf'
                , 'pk_orcamento': this.IdRegistro
                , 'tipo_pdf': 2
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.getCmp('main_statusbar').msg('ok');

                    window.location.href = "Main/PHP/Download_Arquivo.php?file="+response.file+'&path='+response.path+'&filename='+response.filename;

                }
                else {
                    alert('failure');
                }
            }
        });
    }

    , novoOrcamento: function(){
        console.log('Orcamentos_Servidor_Form - novoOrcamento');

        // Identificar o Cliente
        if (this.IdCliente <= 0){
            this.criaWindowPesquisaCliente();
            this.window_pesquisa.show();
            this.hide();
        }
        else {
            //nao sei ainda
        }
    }

    , criaWindowPesquisaCliente: function(){
        console.log('Orcamentos_Servidor_Form - criaWindowPesquisaCliente');

        //if(!this.window_pesquisa) {
            //this.window_pesquisa = null;
            this.window_pesquisa = new Clientes_Localizar_Window({
                listeners  :{
                    scope  : this
                    , seleciona_cliente : this.onSelecionaCliente
                }
            })
        //}
        return this.window_pesquisa
    }

    , onSelecionaCliente: function(obj, clienteId){
        console.log('Orcamentos_Servidor_Form - onSelecionaCliente');

            //this.window_pesquisa.hide();
            this.window_pesquisa.close();
            this.setClienteId(clienteId);
            this.createOrcamento();
    }

    , createOrcamento: function(){
        console.log('Orcamentos_Servidor_Form - createOrcamento');

        Ext.Ajax.request({
            url    : this.main_url
            , params : {
                classe    : 'Orcamentos'
                , action  : 'CriaOrcamento'
                , fk_id_cliente: this.IdCliente
                , identificacao_cliente: 'pk_id_cliente'
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.getCmp('main_statusbar').msg('ok');
                    console.log('Criou o orcamento - '+response.pk_orcamento);
                    this.setRegistroID(response.pk_orcamento);

                    this.fireEvent('novo_orcamento', this, response.pk_orcamento);

                    this.show();
                }
                else {
                    Ext.getCmp('main_statusbar').msg('error');
                    Ext.MessageBox.show({ title:'Desculpe!', msg: response.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
                }
            }
        });
    }

    , onAlterarQuantidade: function(obj, data){
        this.atualizar_valores(obj, data);
        this.formLoad();
    }

    , onIncluirProduto: function(obj, data){
        this.atualizar_valores(obj, data);
        this.formLoad();
    }

    , atualizar_valores: function(obj, data){
        Ext.getCmp('txtValorTotalMain').setValue('R$ ' + data.valor_total);
        Ext.getCmp('txtQtdItensMain').setValue(data.qtd_itens + ' Itens');
        Ext.getCmp('txtNomeClienteMain').setValue(data.nome);
    }

    , atualizar_valores_inicio: function(obj, data){
        Ext.getCmp('txtValorTotalMain').setValue('R$ ' + data.valor_total_entrega);
        Ext.getCmp('txtQtdItensMain').setValue(data.qtd_itens_entregue + ' Itens');
        Ext.getCmp('txtNomeClienteMain').setValue(data.nome_cliente);
    }

    , zerar_valores: function(){

        Ext.getCmp('txtValorTotalMain').setValue('R$ ');
        Ext.getCmp('txtQtdItensMain').setValue('0 Itens');
        Ext.getCmp('txtNomeClienteMain').setValue('');
    }

});
Ext.reg('e-Orcamentos_Servidor_Form', Orcamentos_Servidor_Form);

// ----------------------------------------------//----------------------------------------------

