/**
 * @package     : Clientes
 * @name        : Clientes_Etiquetas_Panel
 * @Diretorio   : Main/Modulos/Clientes/
 * @Dependencias:
 * @date        : 31/01/2011
 */

var Clientes_Etiquetas_Panel = Ext.extend(Ext.Panel,{

    //$depends     : ['']
    id         : 'Clientes_Etiquetas_Panel'
    , identificacao : '4002' // Identificacao para permissoes

    , layout: 'border'
    , autoScroll : true

    , border     : false
    , stripeRows : true
    , loadMask   : true

    ,initComponent: function() {


        Ext.apply(this,{
            items:[{
                xtype: 'e-Clientes_Etiquetas_Form'
            },{
                xtype: 'e-Clientes_Etiquetas_Grid'
            }]
        })
        Clientes_Etiquetas_Panel.superclass.initComponent.call(this);
    }

});
Ext.reg('e-Clientes_Etiquetas_Panel', Clientes_Etiquetas_Panel);


//-----------------------------------//------------------------------//------------------------------//


var Clientes_Etiquetas_Form = Ext.extend(Ext.form.FormPanel,{
    id : 'Clientes_Etiquetas_Form'
    , region : 'north'
    , height : 150
    , bodyStyle:'padding:10px 10px 0'
    , labelAlign: 'top'


    , main_url   : 'main.php'
    , main_class : 'Clientes_Etiquetas'
    , metodo_load  : 'Total_Clientes'
    , metodo_submit: 'criaAtualiza'


    , initComponent: function(){

        var arrCampos = [
            ['pk_id_cliente', 'Código']
            , ['dt_inclusao', 'Data Inclusão']
            , ['dt_alteracao', 'Data Alteração']
        ];

        // ComboBox Lojas
        this.cmbLojas = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'clientes_etiquetas_combo_Lojas'
            , fieldLabel     : 'Loja de Origem'
            , hiddenName     : 'fk_id_loja'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'Nome'
            , emptyText      : 'Selecione uma Loja'
            , width          : 150
            , mode           : 'local'
            //, allowBlank     : false
            , col            : true
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'id'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'Lojas'
                    , action  : 'getLojasCmb'
                }
                , fields:[
                    {name:'id'     , type:'int'}
                    , {name:'Nome' , type:'string'}
                ]
            })
        })

        this.cmbTipo_Cliente = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'etiquetas_combo_tipo_cliente'
            , fieldLabel     : 'Tipo Cliente'
            , hiddenName     : 'tipo_cliente'
            , triggerAction  : 'all'
            , valueField     : 'pk_tipo_cliente'
            , displayField   : 'tipo_cliente'
            , emptyText      : 'Selecione'
            , width          : 150
            , mode           : 'local'
            //, allowBlank     : false
            , col            : true
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'pk_id_cliente'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'Tipo_Cliente'
                    , action  : 'getTipo_ClienteCmb'
                }
                , fields:[
                    {name:'pk_tipo_cliente' , type:'int'}
                    , {name:'tipo_cliente'  , type:'string'}
                ]
            })
        })

        // Store da ComboBox Tipos de Etiquetas
        this.cmbEtiquetas = new Ext.form.ComboBox({
            root             : 'rows'
            , id: 'clientes_etiquetas_combo_etiqueta'
            , fieldLabel: 'Modelo Etiqueta'
            , hiddenName:'tipo_etiqueta'
            , triggerAction  : 'all'
            , valueField     : 'pk_id_etiqueta'
            , displayField   : 'modelo'
            , emptyText      : 'Selecione uma Etiqueta'
            , anchor         : '98%'
            , allowBlank     : false
            , disabled: true
            , store          : new Ext.data.JsonStore({
                url           : this.main_url
                , root        : 'rows'
                , idProperty  : 'pk_id_etiqueta'
                , autoDestroy : true
                , baseParams  : {
                    classe    : 'Etiquetas'
                    , action  : 'get_etiquetas_cmb'
                }
                , fields:[
                    {name:'pk_id_etiqueta'      , type:'int'}
                    , {name:'modelo' , type:'string'}
                ]
            })
        })

        Ext.apply(this,{
            items: [{
                layout:'column'
                , border: false
                , items:[{ // 1 Coluna
                    columnWidth:.8
                    , layout: 'form'
                    , border: false
                    , labelWidth: 120
                    , items:[{
                        xtype:'fieldset'
                        , title: 'Filtro'
                        , anchor:'98%'
                        , height : 120
                        , autoScroll : false
                        , items: [
                        this.cmbLojas
                        , this.cmbTipo_Cliente
                        ,{
                            xtype: 'combo'
                            , id: 'form_campo'
                            , fieldLabel: 'Campo'
                            , hiddenName:'campo'
                            , triggerAction  : 'all'
                            , labelWidth: 120
                            , width: 150
                            , msgTarget: 'side'
                            , allowBlank:false
                            , forceSelection: true
                            , emptyText: 'Selecione um campo'
                            , valueField: 'campo'
                            , displayField:'campo_label'
                            , mode: 'local'
                            , store: new Ext.data.SimpleStore({
                                fields:['campo', 'campo_label']
                                , data:arrCampos
                            })
                            , col:true
                        },{
                            xtype: 'textfield'
                            , fieldLabel: 'Valor inicial'
                            , name      : 'valor_inicial'
                            , labelWidth: 120
                            , width: 120
                            , col:true
                        },{
                            xtype: 'textfield'
                            , fieldLabel: 'Valor Final'
                            , name      : 'valor_final'
                            , labelWidth: 120
                            , width: 120
                            , col:true
                        }]
                        , buttons:[{
                            text:'Pesquisar'
                            , iconCls:'silk-zoom'
                            , scope  : this
                            , handler: this._onBtnPesquisarClick
                        },{
                            text:'Limpar'
                            , scope  : this
                            , handler: this._onBtnLimparClick
                        }]
                    }]
                },{  // 2 Coluna
                    columnWidth:.2
                    , layout: 'form'
                    , border: false
                    , labelWidth: 50
                    , items:[{
                        xtype:'fieldset'
                        , title: 'Etiquetas'
                        , anchor:'98%'
                        , height : 120
                        , autoScroll : false
                        , items: [this.cmbEtiquetas]
                        , buttons:[{
                            text:'Gerar PDF'
                            , id: 'btnGerarPDF'
                            , iconCls:'silk-acrobat'
                            , disabled: true
                            , scope  : this
                            , handler: this._onBtnGerarPDF
                        }]
                    }]
                }]
            }]
        })
        Clientes_Etiquetas_Form.superclass.initComponent.call(this);
    }

    // Listener disparado ao clicar no botão Pesquisar
    , _onBtnPesquisarClick: function() {
        //pego o formulário
        var form = this.getForm();

        //verifico se é valido
        if(!form.isValid()) {
            Ext.Msg.alert('Atenção','Preencha corretamente todos os campos!');
            return false;
        }
        //crio uma máscara
        this.el.mask('Recuperando informa&ccedil;&otilde;es');

        // Submitando formulário
        form.submit({
            url : this.main_url
            , params    : {
                classe   : this.main_class
                , action : this.metodo_load
            }
            , scope:this
            , success: function(form, retorno) {//ao terminar de submitar
                //tirá máscara
                this.el.unmask();
                //esconde janela
                //this.show();
                // Setando os baseParams para a store da Grid
                var filtro = form.getValues();
                Ext.getCmp('Clientes_Etiquetas_Grid').store.baseParams.loja  = filtro.loja;
                Ext.getCmp('Clientes_Etiquetas_Grid').store.baseParams.tipo_cliente = filtro.tipo_cliente;
                Ext.getCmp('Clientes_Etiquetas_Grid').store.baseParams.campo = filtro.campo;
                Ext.getCmp('Clientes_Etiquetas_Grid').store.baseParams.valor_inicial = filtro.valor_inicial;
                Ext.getCmp('Clientes_Etiquetas_Grid').store.baseParams.valor_final = filtro.valor_final;
                Ext.getCmp('Clientes_Etiquetas_Grid').store.load();
                // Ativando a opcao de etiquetas
                Ext.getCmp('clientes_etiquetas_combo_etiqueta').enable(true);
                Ext.getCmp('btnGerarPDF').enable(true);

                this.fireEvent('pesquisar',this);
            }
            , failure: function(r,o) {
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('error');

                Ext.Msg.alert('Alerta!', o.result.msg);
            }
        });
    }

    // Listener disparado ao clicar no botão Pesquisar
    , _onBtnLimparClick: function() {
        // Desativando a opcao de etiquetas
        Ext.getCmp('clientes_etiquetas_combo_etiqueta').disable();
        Ext.getCmp('btnGerarPDF').disable();

        //pego o formulário
        var form = this.getForm();
        form.reset();
        Ext.getCmp('Etiquetas_Clientes_Grid').store.removeAll();
    }

    , _onBtnGerarPDF: function() {
        //pego o formulário
        var form = this.getForm();

        //verifico se é valido
        if(!form.isValid()) {
            Ext.Msg.alert('Atenção','Preencha corretamente todos os campos!');
            return false;
        }
        //crio uma máscara
        this.el.mask('Recuperando informa&ccedil;&otilde;es');

        // Submitando formulário
        form.submit({
            url : this.main_url
            , params    : {
                classe  : this.main_class
                , action  : 'Gerar_Etiquetas'
            }
            , scope:this
            , success: function(r,o) {
                this.el.unmask();
                Ext.Msg.alert('Status', 'Registro(s) exportados(s) com sucesso!<br> Click no Link para baixar o arquivo<br><a href = "Main/Modulos/Clientes/Clientes_Exportar_Excel_Download.php?file='+o.result.file+'&path='+o.result.path+'" target="_blank">Download Arquivo</a></center>');
            }
            , failure: function(form){
                this.el.unmask();
                Ext.Msg.alert('Status','Falha ao gerar o arquivo. <br>Por favor tente novamente, caso o problema persista, entre em contato com o administrador!');
            }
        });
    }


});
Ext.reg('e-Clientes_Etiquetas_Form', Clientes_Etiquetas_Form);



//-----------------------------------//------------------------------//------------------------------//


var Clientes_Etiquetas_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'Clientes_Etiquetas_Grid'
    , region : 'center'
    , loadMask: true

    , main_url     : 'main.php'
    , main_class   : 'Clientes_Etiquetas'
    , metodo_load  : 'load_clientes'

    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url            : this.main_url
            , root           : 'rows'
            , idProperty     : 'pk_id_cliente'
            , totalProperty : 'totalCount'
            //, autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
               classe  : this.main_class
                , action : this.metodo_load
                , limit  : 30
            }
            , fields:[
                {name:'pk_id_cliente'      , type:'int'}
                , {name:'nome'  , type:'string'}
                , {name:'rua' , type:'string'}
                , {name:'numero' , type:'string'}
                , {name:'bairro' , type:'string'}
                , {name:'cidade' , type:'string'}
                , {name:'cep' , type:'string'}
            ]

        });

        Ext.apply(this,{
            viewConfig:{
                emptyText        : 'Nenhum registro encontrado'
                , forceFit       :true
                , deferEmptyText : false
            }
            , defaults: {
                sortable      : false
                , menuDisabled: true
                , hideable    : false
                , groupable   : false
            }
            , bbar: new Ext.PagingToolbar({ //paginação
                store       : this.store
                , pageSize  : 30
                , displayInfo: true
                , displayMsg: 'Mostrando resultados {0} - {1} de {2}'
                , plugins: new Ext.ux.ProgressBarPager()
            })
            , columns:[{
                dataIndex   : 'nome'
                , header    : 'Nome'
                //, width     : 80
            },{
                dataIndex   : 'rua'
                , header    : 'Endereco'
            },{
                dataIndex   : 'numero'
                , header    : 'Numero'
            },{
                dataIndex   : 'bairro'
                , header    : 'Bairro'
            },{
                dataIndex   : 'cidade'
                , header    : 'Cidade'
            },{
                dataIndex   : 'cep'
                , header    : 'Cep'
            }]
        })
        Clientes_Etiquetas_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Clientes_Etiquetas_Grid.superclass.initEvents.call(this);
    }

    , _onRefresh: function(){
        //Ext.Msg.alert('<font color=red>Debug Message!</font>', this.uptime);
        this.store.load();
    }
});
Ext.reg('e-Clientes_Etiquetas_Grid', Clientes_Etiquetas_Grid);
