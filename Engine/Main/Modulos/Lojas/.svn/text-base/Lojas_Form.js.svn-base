/**
 * @date     : 16/06/2010
 * @Diretorio: Main/Modulos/Acesso/
 */
var Lojas_Form = Ext.extend(Ext.Window,{

    id: 'Lojas_Form'
    , IdRegistro: -1

    , modal  : true
    , constrain: true
    //, maximizable: true
    , width  : 480
    , height : 550
    , title  : 'Cadastro de Lojas'
    , layout : 'fit'

    // Essa janela sera reaproveitada, por isso closeAction deve ser HIDE
    , closeAction: 'hide'

    // Permissoes 0 Desabilitado / 1 Habilitado
    , perm_insert: 1
    , perm_delete: 1
    , perm_update: 1
    , perm_impres: 1

    , setPermissaoIns: function(perm_insert) { this.perm_insert = perm_insert; }
    , setPermissaoDel: function(perm_delete) { this.perm_delete = perm_delete; }
    , setPermissaoUpd: function(perm_update) { this.perm_update = perm_update; }
    , setPermissaoImp: function(perm_impres) { this.perm_impres = perm_impres; }

    //id do usuario
    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }

    ,constructor: function() {
    /*
     * Aqui eu adiciono um evento personalizado a classe. Esse evento sera disparado posteriormente quando o usuario
     * clica no botao salvar
     */
        this.addEvents({
            salvar: true
            , excluir: true
        });

        Lojas_Form.superclass.constructor.apply(this, arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

                // ComboBox Grupo de Usuarios
        this.cmbGrupos_Lojas = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'combo_grupos_Lojas'
            , fieldLabel     : 'Grupo'
            , hiddenName     : 'Grupo'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'Grupo'
            , emptyText      : 'Selecione um Grupo'
            , width          : 150
            //, allowBlank     : false
            , col            : true
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'id'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'GruposLojas'
                    , action  : 'getGrupos'
                }
                , fields:[
                    {name:'id'      , type:'int'}
                    , {name:'Grupo' , type:'string'}
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
                fieldLabel   : 'C&oacute;digo'
                , name       : 'id'
                , xtype      : 'textfield'
                , width      : 80
                , readOnly   :true
            },{
                fieldLabel   : 'Nome'
                , name       : 'nome'
                , xtype      : 'textfield'
                , width      : 150
                , col        : true
            }
            , this.cmbGrupos_Lojas
            ,{
                xtype:'fieldset'
                , title: ''
                , anchor:'99%'
                , autoScroll : false
                , items: [{
                    fieldLabel   : 'Raz&atilde;o Social'
                    , name       : 'razao_social'
                    , xtype      : 'textfield'
                    , width      : 410
                    , col        : true
                },{
                    fieldLabel   : 'CNPJ'
                    , name       : 'cnpj'
                    , xtype      : 'textfield'
                    , width      : 150
                },{
                    fieldLabel   : 'Insc. Estadual'
                    , name       : 'inscricao_estadual'
                    , xtype      : 'textfield'
                    , width      : 100
                    , col        : true
                },{
                    fieldLabel   : 'Isnc. Municipal'
                    , name       : 'inscricao_municipal'
                    , xtype      : 'textfield'
                    , width      : 100
                    , col        : true
                }]
            },{
                xtype:'fieldset'
                , title: 'Endere&ccedil;o'
                , anchor:'99%'
                , autoScroll : false
                , items: [{
                    fieldLabel   : 'Rua'
                    , name       : 'rua'
                    , xtype      : 'textfield'
                    , labelWidth : 50
                    , width      : 300
                },{
                    fieldLabel   : 'N&uacute;mero'
                    , name       : 'numero'
                    , xtype      : 'textfield'
                    , labelWidth : 50
                    , width      : 80
                    , col        : true
                },{
                    fieldLabel   : 'Bairro'
                    , name       : 'bairro'
                    , xtype      : 'textfield'
                    , labelWidth : 50
                    , width      : 190
                },{
                    fieldLabel   : 'Cidade'
                    , name       : 'cidade'
                    , xtype      : 'textfield'
                    , labelWidth : 50
                    , width      : 190
                    , col        : true
                },{
                    fieldLabel   : 'Complemento'
                    , name       : 'complemento'
                    , xtype      : 'textfield'
                    , labelWidth : 50
                    , width      : 190
                },{
                    fieldLabel   : 'CEP'
                    , name       : 'cep'
                    , xtype      : 'textfield'
                    , labelWidth : 50
                    , width      : 80
                    , col        : true
                },{
                    xtype        : 'e-Cmb_Uf'
                    , id         : 'lojas_cmb_uf'
                    , hiddenName : 'uf'
                    , width      : 50
                    , col        : true
                }]
            },{
                xtype:'fieldset'
                , title: 'Contatos'
                , anchor:'99%'
                , autoScroll : false
                , items: [{
                    fieldLabel: 'Telefone Fixo'
                    , xtype: 'textfield'
                    , name : 'telefone_fixo'
                    , width: 85
                },{
                    fieldLabel: 'Telefone Movel'
                    , xtype: 'textfield'
                    , name : 'telefone_movel'
                    , width: 85
                    , col  : true
                },{
                    fieldLabel: 'E-mail'
                    , xtype: 'textfield'
                    , name : 'email'
                    , width: 180
                    , col  : true
                }]
            }]
        })

        Ext.apply(this,{
            items  : this.formPanel
            , bbar : ['->'
            , this.btnSalvar = new Ext.Button({
                text     : 'Salvar'
                , iconCls: 'silk-disk'
                , scope  : this
                , handler: this._onBtnSalvarClick
            })
            , this.btnExcluir = new Ext.Button({
                text     : 'Excluir'
                , iconCls: 'silk-delete'
                , scope  : this
                , handler: this._onBtnDeleteClick
            })
            ,{
                text     : 'Cancelar'
                , iconCls: 'silk-cross'
                , scope  : this
                , handler: this._onBtnCancelarClick
            }]
        })
        Lojas_Form.superclass.initComponent.call(this);
    }

    /*
     * Sobreescrevo o metodo show de Ext.Window para aplicar a seguinte rotina:
     * Se IdRegistro foi informado, carrega o form, senao, reseta o form.
     */
    , show: function() {
        Lojas_Form.superclass.show.apply(this,arguments);
        this.formPanel.getForm().reset();
        //se tem usuario
        if(this.IdRegistro !== -1) {
            this.btnExcluir.show();
            // Tratamento de Permissoes
            // o usuario pode excluir?
            if(this.perm_delete == 0){
                this.btnExcluir.disable();
            }
            // o usuario pode alterar?
            if(this.perm_update == 0){
                this.btnSalvar.disable();;
            }

            this.el.mask('Carregando informa&ccedil;&otilde;es');
            Ext.getCmp('main_statusbar').msg('load');

            /*
             * Carregando o formulario. Ele deve respeitar algums formatos especificiados na documentacao ext de
             * Ext.form.Action.Load, como por exemplo conter uma propriedade success e data.
             */
            this.formPanel.getForm().load({
                url : 'main.php'
                , params : {
                    classe   : 'Lojas'
                    , action: 'getLoja'
                    , id    : this.IdRegistro
                }
                , scope: this
                , success: this._onFormLoad
            });
        }
        //se nao existir usuario
        else {
            //nao pode excluir
            this.btnExcluir.hide();
            if(this.perm_insert == 0){
                this.btnSalvar.disable();;
            }
            // Resetando o formulario
            this.formPanel.getForm().reset();
        }
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Lojas_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
    }

    // Listeners

    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;
        // tiro a mascara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
    }

    // Listener disparado ao clicar no botao salvar
    , _onBtnSalvarClick: function() {
        //pego o formulario
        var form = this.formPanel.getForm();
        //verifico se e valido
        if(!form.isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        // crio uma mascara
        this.el.mask('Salvando informa&ccedil;&otilde;es');
        Ext.getCmp('main_statusbar').msg('saving');

        // Submitando formulario
        form.submit({
            url: 'main.php'
            , params: {
                classe   : 'Lojas'
                , action: 'CriaAtualiza'
                , id    : this.IdRegistro
            }
            , scope:this
            //ao terminar de submitar
            , success: function() {
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('save');
                //esconde janela
                this.hide();
                // Muito importante! Aqui o evento salvar sera disparado. Todos os listeners que foram associados a esse evento serao notificados, como por exemplo, o listener _onCadastroGruposUsuarios Salvar da classe Grid.
                this.fireEvent('salvar',this);
            }
        });
    }

    // Listener disparado ao clicar em excluir
    , _onBtnDeleteClick: function() {
        Ext.Msg.confirm('Confirma&ccedil;&otilde;o','Deseja mesmo excluir esse registro?',function(opt) {
            if(opt === 'no') {
                return
            }
            else {
                this.el.mask('Excluindo Registro.');
                Ext.getCmp('main_statusbar').msg('job');
                Ext.Ajax.request({
                    url: 'main.php'
                    , params: {
                        classe   : 'Lojas'
                        , action: 'deleteLojas'
                        , id    : this.IdRegistro
                    }
                    , scope: this
                    , success: function() {
                        this.el.unmask();
                        Ext.getCmp('main_statusbar').msg('ok');
                        this.hide();
                        // Evento personalizado excluir sendo disparado
                        this.fireEvent('excluir',this);
                    }
                })
            }
        },this)
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
});