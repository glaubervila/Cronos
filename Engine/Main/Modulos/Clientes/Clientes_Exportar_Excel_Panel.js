/**
 * @package     : Clientes
 * @name        : Clientes_Exportar_Excel_Panel
 * @Diretorio   : Main/Modulos/Clientes/
 * @date        : 27/01/2011
 */
var Clientes_Exportar_Excel_Panel = Ext.extend(Ext.Window,{

    id: 'Clientes_Exportar_Excel_Panel'
    , identificacao : '4001' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Clientes_Exportar_Excel'
    , metodo_submit: 'Exportar_Excel'

    , IdRegistro: 0

    , modal  : true
    , constrain: true
    //, maximizable: true
    , width  : 600
    , height : 500
    , title  : 'Exporta&ccedil;&atilde;o de Clientes Para MS Excel'
    , layout : 'fit'
    , autoScroll : true

   , closeAction: 'hide'

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

        Clientes_Exportar_Excel_Panel.superclass.constructor.apply(this,arguments);
    }


    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';


        // ComboBox Lojas
        this.cmbLojas = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'exportacao_clientes_excel_combo_Lojas'
            , fieldLabel     : 'Loja de Origem'
            , hiddenName     : 'loja'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'Nome'
            , emptyText      : 'Selecione uma Loja'
            , width          : 150
            , mode           : 'local'
            , allowBlank     : false
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
            , id             : 'exportacao_clientes_excel_combo_tipo_cliente'
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


        // FORMULARIO
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:10px;'
            , border         : false
            , labelAlign     : 'top'
            , items:[{
                xtype:'fieldset'
                , title: 'Op&ccedil;oes de Exporta&ccedil;&atilde;o'
                , anchor:'99%'
                , width: 400
                , autoScroll : false
                , items: [
                    this.cmbLojas
                    , this.cmbTipo_Cliente
                ]
            },{
                xtype:'fieldset'
                , title: 'Sele&ccedil;&atilde;o de Campos'
                , anchor:'99%'
                , width: 400
                , autoScroll : false
                , items: [{
                        xtype: 'checkbox'
                        , name: 'todos_campos'
                        , hideLabel :true
                        , boxLabel: 'Todos os Campos'
                        , checked: true
                        , listeners  :{
                            scope: this
                            , 'check': function() {
                                if(Ext.getCmp('check_campos').disabled == true){
                                    Ext.getCmp('check_campos').enable();
                                }
                                else {
                                    Ext.getCmp('check_campos').disable();
                                }
                            }
                        }
                    },{
                    xtype: 'checkboxgroup'
                    , id: 'check_campos'
                    , hideLabel: true
                    , itemCls: 'x-check-group-alt'
                    , disabled: true
                    , columns: 3
                    , vertical: true
                    , items: [
                        {boxLabel: 'C&oacute;digo' , name: 'cliente-1', inputValue: 'pk_id_cliente', checked: true}
                        , {boxLabel: 'Loja Origem' , name: 'cliente-2', inputValue: 'fk_id_loja', checked: true}
                        , {boxLabel: 'Tipo'        , name: 'cliente-3', inputValue: 'tipo', checked: true}
                        , {boxLabel: 'Status'      , name: 'cliente-4', inputValue: 'status'}
                        , {boxLabel: 'Tipo Cliente', name: 'cliente-5', inputValue: 'tipo_cliente', checked: true}

                        , {boxLabel: 'Nome'        , name: 'cliente-6', inputValue: 'nome', checked: true}
                        , {boxLabel: 'CPF'        , name: 'cliente-7', inputValue: 'cpf', checked: true}
                        , {boxLabel: 'CNPJ'       , name: 'cliente-8', inputValue: 'cnpj', checked: true}
                        , {boxLabel: 'RG'         , name: 'cliente-9', inputValue: 'rg', checked: true}
                        , {boxLabel: 'I.Estadual' , name: 'cliente-10', inputValue: 'inscricao_estadual', checked: true}
                        , {boxLabel: 'Data Nascimento' , name: 'cliente-11', inputValue: 'dt_nascimento'}
                        , {boxLabel: 'Profiss&atilde;o', name: 'cliente-12', inputValue: 'profissao'}

                        , {boxLabel: 'Sexo'        , name: 'cliente-13', inputValue: 'sexo'}
                        , {boxLabel: 'Estado Civil', name: 'cliente-14', inputValue: 'estado_civil'}
                        , {boxLabel: 'Tel Fixo'    , name: 'cliente-15', inputValue: 'telefone_fixo'}
                        , {boxLabel: 'Tel Movel'   , name: 'cliente-16', inputValue: 'telefone_movel'}
                        , {boxLabel: 'E-Mail'      , name: 'cliente-17', inputValue: 'email ', checked: true}

                        , {boxLabel: 'Tipo Endere&ccedil;o', name: 'endereco-18', inputValue: 'tipo_endereco'}
                        , {boxLabel: 'Rua'         , name: 'endereco-19', inputValue: 'rua'}
                        , {boxLabel: 'N&uacute;mero', name: 'endereco-20', inputValue: 'numero'}
                        , {boxLabel: 'Bairro'      , name: 'endereco-21', inputValue: 'bairro'}
                        , {boxLabel: 'Cidade'      , name: 'endereco-22', inputValue: 'cidade'}

                        , {boxLabel: 'UF'          , name: 'endereco-23', inputValue: 'uf'}
                        , {boxLabel: 'Complemento' , name: 'endereco-24', inputValue: 'complemento'}
                        , {boxLabel: 'CEP'         , name: 'endereco-25', inputValue: 'cep'}
                        , {boxLabel: 'Observa&ccedil;&otilde;es', name: 'cliente-26', inputValue: 'observacoes '}
                    ]
                }]
            }]
        })


        Ext.apply(this,{
            items  : this.formPanel
            , bbar : ['->'
            , this.btnSalvar = new Ext.Button({
                text     : 'Exportar'
                , iconCls: 'silk-disk'
                , scope  : this
                , handler: this._onBtnExportarClick
            })
            ,{
                text     : 'Cancelar'
                , iconCls: 'silk-cross'
                , scope  : this
                , handler: this._onBtnCancelarClick
            }]
        })
        Clientes_Exportar_Excel_Panel.superclass.initComponent.call(this);
    }


    , show: function() {
        Clientes_Exportar_Excel_Panel.superclass.show.apply(this,arguments);

    }

    , onDestroy: function() {
        Clientes_Exportar_Excel_Panel.superclass.onDestroy.apply(this,arguments);
    }

    // Listener disparado ao clicar em cancelar
    , _onBtnCancelarClick: function() {
        Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo cancelar esse cadastro?',function(opt) {
            if(opt === 'yes') {
                //esconde window
                this.hide();
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

                Ext.Msg.alert('Status', 'Registro(s) exportados(s) com sucesso!<br>Total: '+o.result.total_exportado+'<br> Click no Link para baixar o arquivo<br><a href = "Main/Modulos/Clientes/Clientes_Exportar_Excel_Download.php?file='+o.result.file+'&path='+o.result.path+'" target="_blank">Download Arquivo</a></center>');
                //alert(o.result.file);
                //esconde janela
                //this.hide();
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