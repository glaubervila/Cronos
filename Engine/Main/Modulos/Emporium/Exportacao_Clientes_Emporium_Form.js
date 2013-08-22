/**
 * @package     : Emporium
 * @name        : Exportacao_Clientes_Emporium_Form
 * @version     : 1.0
 * @Diretorio   : Main/Modulos/Emporium/
 * @date        : 09/12/2010
 * @Dependencias:
 * - Main/Modulos/Emporium/Integracao_Emporium.class.php
 * - Main/Modulos/Lojas/Lojas.class.php
 */
var Exportacao_Clientes_Emporium_Form = Ext.extend(Ext.Panel,{

    id: 'Exportacao_Clientes_Emporium_Form'
    , identificacao : '6002' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Exportacao_Clientes_Emporium'
    , pk_id      : 'pk_id_integracao'
    , metodo_submit: 'Exporta_Clientes'


    //, modal  : true
    , constrain: true
    //, maximized  : true
    //, width  : 400
    //, height : 250
    //, title  : ''
    , layout : 'fit'

    // Essa janela sera reaproveitada, por isso closeAction deve ser HIDE
    , closeAction: 'hide'

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }

    // Permissoes 0 Desabilitado / 1 Habilitado
    , perm_insert: 0
    , perm_delete: 0
    , perm_update: 0
    , perm_impres: 0

    , constructor: function() {
        // Tratamento de Permissoes
        indice = permissoes_store.findExact('identificacao', this.identificacao);
        record = permissoes_store.getAt(indice);
        this.perm_insert = record.data.ins;
        this.perm_delete = record.data.del;
        this.perm_update = record.data.upd;
        this.perm_execut = record.data.exc;
        this.perm_impres = record.data.imp;


        // adiciono um evento a classe. Esse evento sera disparado posteriormente quando o clica no botao salvar
        this.addEvents({
            salvar: true
            , excluir: true
        });

        Exportacao_Clientes_Emporium_Form.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';


        // ComboBox Lojas
        this.cmbLojas = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'exportacao_clientes_combo_Lojas'
            , fieldLabel     : 'Loja de Origem'
            , hiddenName     : 'loja'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'nome'
            , emptyText      : 'Selecione uma Loja'
            , width          : 150
            , mode           : 'local'
            , allowBlank     : false
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
                    , {name:'nome' , type:'string'}
                ]
            })
        })

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:10px;'
            , border         : false
            , autoScroll     : true
            , labelAlign     : 'top'
            , items:[{
                xtype:'fieldset'
                , title: 'Exporta&ccedil;&atilde;o de Clientes Para Emporium'
                , anchor:'99%'
                , width: 400
                , autoScroll : false
                , items: [
                this.cmbLojas
                , {
                    xtype: 'radiogroup'
                    , fieldLabel: 'Tipo Exporta&ccedil;&atilde;o'
                    , columns: 1
                    , items: [
                        { boxLabel: 'Alterados'
                            , name: 'tipo_exportacao'
                            , inputValue: 1
                            , checked: true
                        }
                        ,{ boxLabel: 'Completo'
                            , name: 'tipo_exportacao'
                            , inputValue: 2
                        }
                        ,{ boxLabel: 'Per&iacute;odo'
                            , name: 'tipo_exportacao'
                            , inputValue: 3
                            , listeners  :{
                                scope: this
                                , 'check': function() {
                                   if(Ext.getCmp('txt_dt_inicial').disabled == true){
                                        Ext.getCmp('txt_dt_inicial').enable();
                                        Ext.getCmp('txt_dt_final').enable();
                                   }
                                   else {
                                        Ext.getCmp('txt_dt_inicial').disable();
                                        Ext.getCmp('txt_dt_final').disable();
                                   }
                                }
                            }
                        }
                        ,{ boxLabel: 'Identifica&ccedil;&atilde;o'
                            , name: 'tipo_exportacao'
                            , inputValue: 4
                            , listeners  :{
                                scope: this
                                , 'check': function() {
                                if(Ext.getCmp('txt_valor_inicial').disabled == true){
                                        Ext.getCmp('txt_valor_inicial').enable();
                                        Ext.getCmp('txt_valor_final').enable();
                                }
                                else {
                                        Ext.getCmp('txt_valor_inicial').disable();
                                        Ext.getCmp('txt_valor_final').disable();
                                }
                                }
                            }
                        }
                    ]
                },{
                    fieldLabel : 'Data Inicial'
                    , xtype    : 'datefield'
                    , id       :'txt_dt_inicial'
                    , name     : 'dt_inicial'
                    , width    : 120
                    , disabled : true
                    , allowBlank : false
                },{
                    fieldLabel : 'Data Final'
                    , xtype    : 'datefield'
                    , id       :'txt_dt_final'
                    , name     : 'dt_final'
                    , width    : 120
                    , disabled : true
                    , col      : true
                },{
                    fieldLabel : 'Valor Inicial'
                    , xtype    : 'textfield'
                    , id       :'txt_valor_inicial'
                    , name     : 'valor_inicial'
                    , width    : 120
                    , disabled : true
                    , allowBlank : false
                },{
                    fieldLabel : 'Valor Final'
                    , xtype    : 'textfield'
                    , id       :'txt_valor_final'
                    , name     : 'valor_final'
                    , width    : 120
                    , disabled : true
                    , col      : true
                },{
                    xtype:'fieldset'
                    , title: 'Opcionais'
                    , anchor:'99%'
                    , width: 400
                    , autoScroll : false
                    , items: [{
                        xtype: 'checkbox'
                        , name: 'compactar'
                        , hideLabel :true
                        , boxLabel  : 'Compactar arquivo (.zip)'
                        , checked   : true
                    },{
                        xtype: 'checkbox'
                        , name      : 'backup'
                        , hideLabel :true
                        , boxLabel  : 'Guardar Copia no Servidor'
                        , checked   : true
                    },{
                        xtype: 'checkbox'
                        , name: 'apagar'
                        , hideLabel :true
                        , boxLabel: 'Marcar Flag do emporium para apagar registros'
                    },{
                        xtype: 'checkbox'
                        , name: 'todos_mesma_loja'
                        , hideLabel :true
                        , boxLabel: 'Marcar Todos Clientes com Loja 1'
                        , listeners  :{
                            scope: this
                            , 'check': function() {
                                if(Ext.getCmp('exportacao_clientes_combo_Lojas').disabled == true){
                                    Ext.getCmp('exportacao_clientes_combo_Lojas').enable();
                                }
                                else {
                                    Ext.getCmp('exportacao_clientes_combo_Lojas').disable();
                                }
                            }
                        }
                    }]
                }]
            }]
        })

        Ext.apply(this,{
            items  : this.formPanel
            , tbar : [
            this.btnExportar = new Ext.Button({
                text     : 'Exportar'
                , iconCls: 'silk-database-go'
                , scope  : this
                , handler: this._onBtnExportarClick
            })
            ]
        })
        Exportacao_Clientes_Emporium_Form.superclass.initComponent.call(this);
    }
    , show: function() {
        Exportacao_Clientes_Emporium_Form.superclass.show.apply(this,arguments);
        //this.formPanel.getForm().reset();
        if(this.perm_execut == 0){
             this.btnExportar.disable();;
        }
        //this.formPanel.getForm().reset();
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Exportacao_Clientes_Emporium_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
        Ext.getCmp('main_statusbar').clearStatus();
    }

    // Listeners
    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;
        // tiro a mascara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
    }

    // Listener disparado ao clicar em cancelar
    , _onBtnCancelarClick: function() {
        Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo cancelar essa Opera&ccedil;&atilde;o?',function(opt) {
            if(opt === 'yes') {
                //esconde window
               this.formPanel.getForm().reset();
            }
        },this)
    }
    , _onBtnExportarClick: function() {
        //pego o formulario
        var form = this.formPanel.getForm();
        //verifico se e valido
        if(!form.isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        // crio uma mascara
        this.el.mask('Enviando Solicita&ccedil;&atilde;o');
        Ext.getCmp('main_statusbar').msg('job');

        // Submitando formulario
        form.submit({
            url: 'main.php'
            , params: {
                classe   : this.main_class
                , action : this.metodo_submit
            }
            , scope:this
            //ao terminar de submitar
            , success: function(r,o) {
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('ok');

                if(o.result.file){
                    Ext.Msg.alert('Status', 'Registro(s) exportados(s) com sucesso!<br>Total: '+o.result.total+'<br> Click no Link para baixar o arquivo<br><a href = "Main/PHP/Download_Arquivo.php?file='+o.result.file+'&path='+o.result.path+'" target="_blank">Download Arquivo</a></center>');
                }
                else {
                    Ext.Msg.alert('Sucesso ao Exportar os Registros', o.result.msg);
                }
            }
            , failure: function(r,o) {
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('error');

                Ext.Msg.alert('Falha ao Exportar os Registros', o.result.msg);
            }
        });
    }
});
Ext.reg('e-Exportacao_Clientes_Emporium_Form', Exportacao_Clientes_Emporium_Form);